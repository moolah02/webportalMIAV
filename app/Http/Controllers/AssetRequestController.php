<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetRequestItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active.employee']);
    }

    // Shopping Cart - Browse Assets
    public function catalog(Request $request)
    {
        $query = Asset::where('is_requestable', true)
            ->where('status', 'asset-active') // Fixed: Use correct status format
            ->where('stock_quantity', '>', 0); // Only show in-stock items

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $assets = $query->latest()->paginate(12);

        // Get actual categories from database
        $categories = Category::ofType('asset_category')
            ->active()
            ->ordered()
            ->pluck('name')
            ->toArray();

        // Get cart item count
        $cart = session('asset_cart', []);
        $cartItemCount = array_sum(array_column($cart, 'quantity'));

        return view('asset-requests.catalog', compact('assets', 'categories', 'cartItemCount'));
    }

    // Add to Cart
    public function addToCart(Request $request, Asset $asset)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $asset->stock_quantity,
        ]);

        if (!$asset->canBeRequested($request->quantity)) {
            return back()->with('error', 'This asset cannot be requested or insufficient stock.');
        }

        $cart = session('asset_cart', []);
        $assetId = $asset->id;

        if (isset($cart[$assetId])) {
            $newQuantity = $cart[$assetId]['quantity'] + $request->quantity;
            // Check if new quantity exceeds stock
            if ($newQuantity > $asset->stock_quantity) {
                return back()->with('error', 'Cannot add more items. Stock limit would be exceeded.');
            }
            $cart[$assetId]['quantity'] = $newQuantity;
        } else {
            $cart[$assetId] = [
                'asset_id' => $asset->id,
                'name' => $asset->name,
                'unit_price' => $asset->unit_price,
                'quantity' => $request->quantity,
                'image_url' => $asset->image_url,
            ];
        }

        session(['asset_cart' => $cart]);

        return back()->with('success', 'Asset added to cart successfully!');
    }

    // View Cart
    public function cart()
    {
        $cart = session('asset_cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $asset = Asset::find($item['asset_id']);
            if ($asset && $asset->canBeRequested()) {
                $item['asset'] = $asset;
                $item['subtotal'] = $item['quantity'] * $item['unit_price'];
                $total += $item['subtotal'];
                $cartItems[] = $item;
            }
        }

        return view('asset-requests.cart', compact('cartItems', 'total'));
    }

    // Update Cart Item
    public function updateCart(Request $request, $assetId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session('asset_cart', []);

        if ($request->quantity == 0) {
            unset($cart[$assetId]);
            session(['asset_cart' => $cart]);
            return back()->with('success', 'Item removed from cart!');
        } else {
            $asset = Asset::find($assetId);
            if ($asset && $request->quantity <= $asset->stock_quantity) {
                $cart[$assetId]['quantity'] = $request->quantity;
                session(['asset_cart' => $cart]);
                return back()->with('success', 'Cart updated successfully!');
            } else {
                return back()->with('error', 'Invalid quantity or insufficient stock.');
            }
        }
    }

    // Remove from Cart
    public function removeFromCart($assetId)
    {
        $cart = session('asset_cart', []);
        unset($cart[$assetId]);
        session(['asset_cart' => $cart]);

        return back()->with('success', 'Item removed from cart!');
    }

    // Checkout - Create Request
    public function checkout()
    {
        $cart = session('asset_cart', []);
        
        if (empty($cart)) {
            return redirect()->route('asset-requests.catalog')
                ->with('error', 'Your cart is empty.');
        }

        // Calculate total and validate cart items
        $cartItems = [];
        $total = 0;

        foreach ($cart as $item) {
            $asset = Asset::find($item['asset_id']);
            if ($asset && $asset->canBeRequested($item['quantity'])) {
                $item['asset'] = $asset;
                $item['subtotal'] = $item['quantity'] * $item['unit_price'];
                $total += $item['subtotal'];
                $cartItems[] = $item;
            }
        }

        return view('asset-requests.checkout', compact('cartItems', 'total'));
    }

    // Submit Request
    public function store(Request $request)
    {
        $request->validate([
            'business_justification' => 'required|string|min:20',
            'needed_by_date' => 'nullable|date|after:today',
            'delivery_instructions' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $cart = session('asset_cart', []);
        
        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            // Calculate total cost
            $totalCost = 0;
            foreach ($cart as $item) {
                $totalCost += $item['quantity'] * $item['unit_price'];
            }

            // Create the main request
            $assetRequest = AssetRequest::create([
                'employee_id' => auth()->id(),
                'business_justification' => $request->business_justification,
                'needed_by_date' => $request->needed_by_date,
                'delivery_instructions' => $request->delivery_instructions,
                'priority' => $request->priority,
                'total_estimated_cost' => $totalCost,
                'department' => auth()->user()->department->name ?? null,
                'status' => 'pending',
            ]);

            // Create request items from cart
            foreach ($cart as $item) {
                $asset = Asset::find($item['asset_id']);
                
                if ($asset && $asset->canBeRequested($item['quantity'])) {
                    AssetRequestItem::create([
                        'asset_request_id' => $assetRequest->id,
                        'asset_id' => $asset->id,
                        'quantity_requested' => $item['quantity'],
                        'unit_price_at_request' => $asset->unit_price,
                        'total_price' => $item['quantity'] * $asset->unit_price,
                        'item_status' => 'pending',
                    ]);
                }
            }

            // Clear cart
            session()->forget('asset_cart');

            DB::commit();

            return redirect()->route('asset-requests.show', $assetRequest)
                ->with('success', 'Your asset request has been submitted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Asset request submission failed: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Failed to submit request. Please try again.')
                ->withInput();
        }
    }

    // View My Requests
    public function index(Request $request)
    {
        $query = AssetRequest::where('employee_id', auth()->id())
            ->with(['items.asset', 'approver']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return view('asset-requests.index', compact('requests'));
    }

    // View Specific Request
    public function show(AssetRequest $assetRequest)
    {
        // Check if user can view this request
        if ($assetRequest->employee_id !== auth()->id() && 
            !auth()->user()->hasPermission('manage_assets') && 
            !auth()->user()->hasPermission('all')) {
            abort(403);
        }

        $assetRequest->load(['items.asset', 'employee.role', 'employee.department', 'approver', 'fulfiller']);

        return view('asset-requests.show', compact('assetRequest'));
    }

    // Cancel Request (before approval)
    public function cancel(AssetRequest $assetRequest)
    {
        if ($assetRequest->employee_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($assetRequest->status, ['pending', 'draft'])) {
            return back()->with('error', 'Cannot cancel this request.');
        }

        $assetRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'Request cancelled successfully.');
    }

    // Get cart count for AJAX requests
    public function getCartCount()
    {
        $cart = session('asset_cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        
        return response()->json(['count' => $count]);
    }
}