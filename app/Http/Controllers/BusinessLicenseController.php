<?php

namespace App\Http\Controllers;

use App\Models\BusinessLicense;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessLicenseController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'can:manage_assets']);
    }

    public function index(Request $request)
    {
        $direction = $request->get('direction', 'company_held');
        
        $query = BusinessLicense::where('license_direction', $direction)
            ->with(['department', 'responsibleEmployee']);

        // Apply direction-specific filters
        $this->applyFilters($query, $request, $direction);

        // Sort by expiry date by default (upcoming expirations first)
        $sortField = $request->get('sort', 'expiry_date');
        $sortDirection = $request->get('direction_sort', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $licenses = $query->paginate(15)->appends($request->query());

        // Get direction-specific statistics
        $stats = $this->getStatsByDirection($direction);

        // Get departments for filter
        $departments = Department::all();

        return view('business-licenses.index', compact('licenses', 'stats', 'departments', 'direction'));
    }

    private function applyFilters($query, $request, $direction)
    {
        // Common filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_type')) {
            $query->where('license_type', $request->license_type);
        }

        if ($request->filled('priority')) {
            $query->where('priority_level', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $direction) {
                $q->where('license_name', 'LIKE', "%{$search}%")
                  ->orWhere('license_number', 'LIKE', "%{$search}%")
                  ->orWhere('issuing_authority', 'LIKE', "%{$search}%");
                
                // Add customer-specific search for customer-issued licenses
                if ($direction === 'customer_issued') {
                    $q->orWhere('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('customer_company', 'LIKE', "%{$search}%")
                      ->orWhere('customer_email', 'LIKE', "%{$search}%");
                }
            });
        }

        // Direction-specific filters
        if ($direction === 'company_held') {
            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }
        } else {
            // Customer-issued specific filters
            if ($request->filled('billing_cycle')) {
                $query->where('billing_cycle', $request->billing_cycle);
            }
            
            if ($request->filled('support_level')) {
                $query->where('support_level', $request->support_level);
            }
        }
    }

    private function getStatsByDirection($direction)
    {
        if ($direction === 'company_held') {
            return $this->getCompanyLicenseStats();
        } else {
            return $this->getCustomerLicenseStats();
        }
    }

    private function getCompanyLicenseStats()
    {
        return [
            'total_licenses' => BusinessLicense::companyHeld()->count(),
            'active_licenses' => BusinessLicense::companyHeld()->where('status', 'active')->count(),
            'expired_licenses' => BusinessLicense::companyHeld()->expired()->count(),
            'expiring_soon' => BusinessLicense::companyHeld()->expiringSoon(30)->count(),
            'critical_licenses' => BusinessLicense::companyHeld()->where('priority_level', 'critical')->count(),
            'total_annual_cost' => BusinessLicense::companyHeld()->where('status', 'active')->sum('renewal_cost') ?? 0,
        ];
    }

    private function getCustomerLicenseStats()
    {
        $customerLicenses = BusinessLicense::customerIssued();
        
        return [
            'total_licenses' => $customerLicenses->count(),
            'active_licenses' => $customerLicenses->where('status', 'active')->count(),
            'expired_licenses' => $customerLicenses->expired()->count(),
            'expiring_soon' => $customerLicenses->expiringSoon(30)->count(),
            'unique_customers' => $customerLicenses->whereNotNull('customer_email')->distinct('customer_email')->count(),
            'total_revenue' => $customerLicenses->where('status', 'active')->get()->sum('annual_revenue') ?? 0,
        ];
    }

    public function show(BusinessLicense $businessLicense)
    {
        $businessLicense->load(['department', 'responsibleEmployee', 'creator', 'updater']);
        
        return view('business-licenses.show', compact('businessLicense'));
    }

    public function create(Request $request)
    {
        $direction = $request->get('direction', 'company_held');
        $departments = Department::all();
        $employees = Employee::active()->get();
        
        return view('business-licenses.create', compact('departments', 'employees', 'direction'));
    }

    public function store(Request $request)
    {
        $direction = $request->get('license_direction', 'company_held');
        
        // Base validation rules
        $rules = [
            'license_direction' => 'required|in:company_held,customer_issued',
            'license_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:business_licenses',
            'license_type' => 'required|string',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ];

        // Direction-specific validation
        if ($direction === 'company_held') {
            $rules = array_merge($rules, [
                'cost' => 'nullable|numeric|min:0',
                'renewal_cost' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'responsible_employee_id' => 'nullable|exists:employees,id',
                'compliance_notes' => 'nullable|string',
                'renewal_reminder_days' => 'nullable|integer|min:1|max:365',
                'auto_renewal' => 'boolean',
                'priority_level' => 'required|string',
                'business_impact' => 'nullable|string',
                'regulatory_body' => 'nullable|string|max:255',
                'license_conditions' => 'nullable|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_company' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_address' => 'nullable|string',
                'revenue_amount' => 'required|numeric|min:0',
                'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
                'license_terms' => 'nullable|string',
                'usage_limit' => 'nullable|string|max:255',
                'support_level' => 'required|in:basic,standard,premium,enterprise',
                'customer_reference' => 'nullable|string|max:255',
                'service_start_date' => 'nullable|date',
                'license_quantity' => 'nullable|integer|min:1',
                'auto_renewal_customer' => 'boolean',
            ]);
        }

        $validated = $request->validate($rules);

        // Handle file upload
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('business-licenses', 'public');
            $validated['document_path'] = $path;
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $license = BusinessLicense::create($validated);

        return redirect()->route('business-licenses.show', $license)
            ->with('success', 'Business license created successfully!');
    }

    public function edit(BusinessLicense $businessLicense)
    {
        $departments = Department::all();
        $employees = Employee::active()->get();
        
        return view('business-licenses.edit', compact('businessLicense', 'departments', 'employees'));
    }

    public function update(Request $request, BusinessLicense $businessLicense)
    {
        $direction = $businessLicense->license_direction;
        
        // Base validation rules
        $rules = [
            'license_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:business_licenses,license_number,' . $businessLicense->id,
            'license_type' => 'required|string',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ];

        // Direction-specific validation
        if ($direction === 'company_held') {
            $rules = array_merge($rules, [
                'cost' => 'nullable|numeric|min:0',
                'renewal_cost' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'responsible_employee_id' => 'nullable|exists:employees,id',
                'compliance_notes' => 'nullable|string',
                'renewal_reminder_days' => 'nullable|integer|min:1|max:365',
                'auto_renewal' => 'boolean',
                'priority_level' => 'required|string',
                'business_impact' => 'nullable|string',
                'regulatory_body' => 'nullable|string|max:255',
                'license_conditions' => 'nullable|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_company' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_address' => 'nullable|string',
                'revenue_amount' => 'required|numeric|min:0',
                'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
                'license_terms' => 'nullable|string',
                'usage_limit' => 'nullable|string|max:255',
                'support_level' => 'required|in:basic,standard,premium,enterprise',
                'customer_reference' => 'nullable|string|max:255',
                'service_start_date' => 'nullable|date',
                'license_quantity' => 'nullable|integer|min:1',
                'auto_renewal_customer' => 'boolean',
            ]);
        }

        $validated = $request->validate($rules);

        // Handle file upload
        if ($request->hasFile('document')) {
            // Delete old file if exists
            if ($businessLicense->document_path) {
                Storage::disk('public')->delete($businessLicense->document_path);
            }
            $path = $request->file('document')->store('business-licenses', 'public');
            $validated['document_path'] = $path;
        }

        $validated['updated_by'] = auth()->id();

        $businessLicense->update($validated);

        return redirect()->route('business-licenses.show', $businessLicense)
            ->with('success', 'Business license updated successfully!');
    }

    public function destroy(BusinessLicense $businessLicense)
    {
        // Delete associated file
        if ($businessLicense->document_path) {
            Storage::disk('public')->delete($businessLicense->document_path);
        }

        $businessLicense->delete();

        return redirect()->route('business-licenses.index', ['direction' => $businessLicense->license_direction])
            ->with('success', 'Business license deleted successfully!');
    }

    public function renew(BusinessLicense $businessLicense)
    {
        return view('business-licenses.renew', compact('businessLicense'));
    }

    public function processRenewal(Request $request, BusinessLicense $businessLicense)
    {
        $validated = $request->validate([
            'new_expiry_date' => 'required|date|after:today',
            'renewal_cost' => 'nullable|numeric|min:0',
            'compliance_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        // Handle new document upload
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('business-licenses', 'public');
            $businessLicense->update(['document_path' => $path]);
        }

        // Mark as renewed
        $businessLicense->markAsRenewed(
            $validated['new_expiry_date'],
            $validated['renewal_cost'] ?? null
        );

        if (!empty($validated['compliance_notes'])) {
            $businessLicense->update(['compliance_notes' => $validated['compliance_notes']]);
        }

        return redirect()->route('business-licenses.show', $businessLicense)
            ->with('success', 'License renewed successfully!');
    }

    public function downloadDocument(BusinessLicense $businessLicense)
    {
        if (!$businessLicense->document_path || !Storage::disk('public')->exists($businessLicense->document_path)) {
            return back()->with('error', 'Document not found.');
        }

        return Storage::disk('public')->download(
            $businessLicense->document_path,
            $businessLicense->license_name . '_document.pdf'
        );
    }

    public function expiring(Request $request)
    {
        $direction = $request->get('direction', 'company_held');
        $days = $request->get('days', 30);
        
        $licenses = BusinessLicense::where('license_direction', $direction)
            ->expiringSoon($days)
            ->with(['department', 'responsibleEmployee'])
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('business-licenses.expiring', compact('licenses', 'days', 'direction'));
    }

    public function compliance(Request $request)
    {
        $direction = $request->get('direction', 'company_held');
        
        $baseQuery = BusinessLicense::where('license_direction', $direction);
        
        $compliant = $baseQuery->clone()->active()
            ->where('expiry_date', '>', now()->addDays(30))
            ->count();

        $warning = $baseQuery->clone()->expiringSoon(30)->count();
        $nonCompliant = $baseQuery->clone()->expired()->count();

        $licenses = $baseQuery->clone()
            ->with(['department', 'responsibleEmployee'])
            ->where(function($query) {
                $query->where('expiry_date', '<=', now()->addDays(30))
                      ->orWhere('expiry_date', '<', now());
            })
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        $stats = compact('compliant', 'warning', 'nonCompliant');

        return view('business-licenses.compliance', compact('licenses', 'stats', 'direction'));
    }
    // Add this method to your BusinessLicenseController class

public function getFilteredStats(Request $request)
{
    $direction = $request->get('direction', 'company_held');
    
    // Start with base query for the direction
    $query = BusinessLicense::where('license_direction', $direction);
    
    // Apply the same filters as in the main index
    $this->applyFilters($query, $request, $direction);
    
    // Get the filtered licenses
    $filteredLicenses = $query->get();
    
    // Calculate stats based on filtered results
    if ($direction === 'company_held') {
        $stats = [
            'total_licenses' => $filteredLicenses->count(),
            'active_licenses' => $filteredLicenses->where('status', 'active')->count(),
            'expired_licenses' => $filteredLicenses->filter(function($license) {
                return $license->is_expired;
            })->count(),
            'expiring_soon' => $filteredLicenses->filter(function($license) {
                return $license->is_expiring_soon && !$license->is_expired;
            })->count(),
            'critical_licenses' => $filteredLicenses->where('priority_level', 'critical')->count(),
            'total_annual_cost' => $filteredLicenses->where('status', 'active')->sum('renewal_cost') ?? 0,
        ];
    } else {
        $stats = [
            'total_licenses' => $filteredLicenses->count(),
            'active_licenses' => $filteredLicenses->where('status', 'active')->count(),
            'expired_licenses' => $filteredLicenses->filter(function($license) {
                return $license->is_expired;
            })->count(),
            'expiring_soon' => $filteredLicenses->filter(function($license) {
                return $license->is_expiring_soon && !$license->is_expired;
            })->count(),
            'unique_customers' => $filteredLicenses->whereNotNull('customer_email')->unique('customer_email')->count(),
            'total_revenue' => $filteredLicenses->where('status', 'active')->sum(function($license) {
                return $license->annual_revenue;
            }) ?? 0,
        ];
    }
    
    return response()->json([
        'success' => true,
        'stats' => $stats,
        'direction' => $direction
    ]);
}
}