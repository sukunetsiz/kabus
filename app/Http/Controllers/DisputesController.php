<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputesController extends Controller
{
    /**
     * Display a listing of the disputes for the authenticated user.
     */
    public function index()
    {
        $disputes = Dispute::getUserDisputes(Auth::id());
        
        return view('disputes.index', [
            'disputes' => $disputes
        ]);
    }

    /**
     * Display the specified dispute.
     */
    public function show($id)
    {
        $dispute = Dispute::with(['order', 'messages.user'])->findOrFail($id);
        
        // Check if the user is the buyer, vendor, or admin
        $user = Auth::user();
        $isBuyer = $dispute->order->user_id === $user->id;
        $isVendor = $dispute->order->vendor_id === $user->id;
        $isAdmin = $user->hasRole('admin');
        
        if (!$isBuyer && !$isVendor && !$isAdmin) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('disputes.show', [
            'dispute' => $dispute,
            'isBuyer' => $isBuyer,
            'isVendor' => $isVendor,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Open a dispute for an order.
     */
    public function store(Request $request, $uniqueUrl)
    {
        $order = Orders::findByUrl($uniqueUrl);
        
        if (!$order) {
            abort(404);
        }
        
        // Verify ownership - only the buyer can open a dispute
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validate the request data
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);
        
        // Open the dispute
        $dispute = $order->openDispute($validated['reason']);
        
        if (!$dispute) {
            return redirect()->route('orders.show', $order->unique_url)
                ->with('error', 'Unable to open dispute. Disputes can only be opened for orders with "Product Delivered" status.');
        }
        
        // Add the initial message from the buyer
        $message = new DisputeMessage([
            'dispute_id' => $dispute->id,
            'user_id' => Auth::id(),
            'message' => $validated['reason']
        ]);
        $message->save();
        
        return redirect()->route('disputes.show', $dispute->id)
            ->with('success', 'Dispute opened successfully.');
    }

    /**
     * Add a message to a dispute.
     */
    public function addMessage(Request $request, $id)
    {
        $dispute = Dispute::findOrFail($id);
        
        // Check if the user is the buyer, vendor, or admin
        $user = Auth::user();
        $isBuyer = $dispute->order->user_id === $user->id;
        $isVendor = $dispute->order->vendor_id === $user->id;
        $isAdmin = $user->hasRole('admin');
        
        if (!$isBuyer && !$isVendor && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if dispute is active
        if ($dispute->status !== Dispute::STATUS_ACTIVE) {
            return redirect()->route('disputes.show', $dispute->id)
                ->with('error', 'Cannot add messages to a resolved dispute.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:1000',
        ]);
        
        // Add the message
        $message = new DisputeMessage([
            'dispute_id' => $dispute->id,
            'user_id' => Auth::id(),
            'message' => $validated['message']
        ]);
        $message->save();
        
        return redirect()->route('disputes.show', $dispute->id)
            ->with('success', 'Message added successfully.');
    }

    /**
     * Display a listing of all disputes for admin.
     */
    public function adminIndex()
    {
        // Query all disputes, ordering by created_at descending
        $disputes = Dispute::orderBy('created_at', 'desc')->get();
        
        return view('admin.disputes.index', [
            'disputes' => $disputes
        ]);
    }

    /**
     * Display a specific dispute for admin.
     */
    public function adminShow($id)
    {
        $dispute = Dispute::with(['order', 'order.user', 'order.vendor', 'messages.user'])->findOrFail($id);
        
        return view('admin.disputes.show', [
            'dispute' => $dispute
        ]);
    }

    /**
     * Resolve a dispute with vendor prevailing.
     */
    public function resolveVendorPrevails(Request $request, $id)
    {
        $dispute = Dispute::findOrFail($id);
        
        // Ensure dispute is active
        if ($dispute->status !== Dispute::STATUS_ACTIVE) {
            return redirect()->route('admin.disputes.show', $dispute->id)
                ->with('error', 'This dispute has already been resolved.');
        }
        
        // Resolve the dispute with vendor prevailing
        if ($dispute->resolveVendorPrevails(Auth::id())) {
            // Add admin's resolution message if provided
            if ($request->has('message') && !empty($request->message)) {
                $message = new DisputeMessage([
                    'dispute_id' => $dispute->id,
                    'user_id' => Auth::id(),
                    'message' => $request->message
                ]);
                $message->save();
            }
            
            return redirect()->route('admin.disputes.show', $dispute->id)
                ->with('success', 'Dispute resolved in favor of the vendor.');
        }
        
        return redirect()->route('admin.disputes.show', $dispute->id)
            ->with('error', 'Unable to resolve dispute.');
    }

    /**
     * Resolve a dispute with buyer prevailing.
     */
    public function resolveBuyerPrevails(Request $request, $id)
    {
        $dispute = Dispute::findOrFail($id);
        
        // Ensure dispute is active
        if ($dispute->status !== Dispute::STATUS_ACTIVE) {
            return redirect()->route('admin.disputes.show', $dispute->id)
                ->with('error', 'This dispute has already been resolved.');
        }
        
        // Resolve the dispute with buyer prevailing
        if ($dispute->resolveBuyerPrevails(Auth::id())) {
            // Add admin's resolution message if provided
            if ($request->has('message') && !empty($request->message)) {
                $message = new DisputeMessage([
                    'dispute_id' => $dispute->id,
                    'user_id' => Auth::id(),
                    'message' => $request->message
                ]);
                $message->save();
            }
            
            return redirect()->route('admin.disputes.show', $dispute->id)
                ->with('success', 'Dispute resolved in favor of the buyer.');
        }
        
        return redirect()->route('admin.disputes.show', $dispute->id)
            ->with('error', 'Unable to resolve dispute.');
    }

    /**
     * Display a listing of disputes for a vendor.
     */
    public function vendorDisputes()
    {
        $disputes = Dispute::getVendorDisputes(Auth::id());
        
        return view('vendor.disputes.index', [
            'disputes' => $disputes
        ]);
    }

    /**
     * Display a specific dispute for a vendor.
     */
    public function vendorShow($id)
    {
        $dispute = Dispute::with(['order', 'order.user', 'messages.user'])->findOrFail($id);
        
        // Verify the vendor owns this dispute
        if ($dispute->order->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('vendor.disputes.show', [
            'dispute' => $dispute
        ]);
    }
}
