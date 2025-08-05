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
        $query = BusinessLicense::with(['department', 'responsibleEmployee']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_type')) {
            $query->where('license_type', $request->license_type);
        }

        if ($request->filled('priority')) {
            $query->where('priority_level', $request->priority);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('license_name', 'LIKE', "%{$search}%")
                  ->orWhere('license_number', 'LIKE', "%{$search}%")
                  ->orWhere('issuing_authority', 'LIKE', "%{$search}%");
            });
        }

        // Sort by expiry date by default (upcoming expirations first)
        $sortField = $request->get('sort', 'expiry_date');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $licenses = $query->paginate(15);

        // Statistics for dashboard
        $stats = [
            'total_licenses' => BusinessLicense::count(),
            'active_licenses' => BusinessLicense::where('status', 'active')->count(),
            'expired_licenses' => BusinessLicense::expired()->count(),
            'expiring_soon' => BusinessLicense::expiringSoon(30)->count(),
            'critical_licenses' => BusinessLicense::where('priority_level', 'critical')->count(),
            'total_annual_cost' => BusinessLicense::where('status', 'active')->sum('renewal_cost'),
        ];

        // Get departments for filter
        $departments = Department::all();

        return view('business-licenses.index', compact('licenses', 'stats', 'departments'));
    }

    public function show(BusinessLicense $businessLicense)
    {
        $businessLicense->load(['department', 'responsibleEmployee', 'creator', 'updater']);
        
        return view('business-licenses.show', compact('businessLicense'));
    }

    public function create()
    {
        $departments = Department::all();
        $employees = Employee::active()->get();
        
        return view('business-licenses.create', compact('departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:business_licenses',
            'license_type' => 'required|string',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'renewal_cost' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'responsible_employee_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'renewal_reminder_days' => 'nullable|integer|min:1|max:365',
            'auto_renewal' => 'boolean',
            'priority_level' => 'required|string',
            'business_impact' => 'nullable|string',
            'regulatory_body' => 'nullable|string|max:255',
            'license_conditions' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

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
        $validated = $request->validate([
            'license_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:business_licenses,license_number,' . $businessLicense->id,
            'license_type' => 'required|string',
            'issuing_authority' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
            'renewal_cost' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'responsible_employee_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'renewal_reminder_days' => 'nullable|integer|min:1|max:365',
            'auto_renewal' => 'boolean',
            'priority_level' => 'required|string',
            'business_impact' => 'nullable|string',
            'regulatory_body' => 'nullable|string|max:255',
            'license_conditions' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

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

        return redirect()->route('business-licenses.index')
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
        $days = $request->get('days', 30);
        $licenses = BusinessLicense::expiringSoon($days)
            ->with(['department', 'responsibleEmployee'])
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return view('business-licenses.expiring', compact('licenses', 'days'));
    }

    public function compliance()
    {
        $compliant = BusinessLicense::active()
            ->where('expiry_date', '>', now()->addDays(30))
            ->count();

        $warning = BusinessLicense::expiringSoon(30)->count();
        $nonCompliant = BusinessLicense::expired()->count();

        $licenses = BusinessLicense::with(['department', 'responsibleEmployee'])
            ->where(function($query) {
                $query->where('expiry_date', '<=', now()->addDays(30))
                      ->orWhere('expiry_date', '<', now());
            })
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        $stats = compact('compliant', 'warning', 'nonCompliant');

        return view('business-licenses.compliance', compact('licenses', 'stats'));
    }
}