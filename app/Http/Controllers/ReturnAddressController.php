<?php

namespace App\Http\Controllers;

use App\Models\ReturnAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use MoneroIntegrations\MoneroPhp\Cryptonote;

class ReturnAddressController extends Controller
{
    /**
     * The cryptonote instance.
     */
    private $cryptonote;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        try {
            $this->cryptonote = new cryptonote();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Monero cryptonote: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource and the form to add a new address.
     */
    public function index()
    {
        $returnAddresses = Auth::user()->returnAddresses;
        return view('return-addresses', compact('returnAddresses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'monero_address' => [
                'required',
                'string',
                'min:40',
                'max:160',
                Rule::unique('return_addresses')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessage = $errors->first();
            return redirect()->route('return-addresses.index')->with('error', $errorMessage)->withInput();
        }

        // Validate the address using monerophp cryptonote.
        $isValid = false;
        try {
            // First check if the address has a valid format and checksum
            $isValid = $this->cryptonote->verify_checksum($request->monero_address);
            
            if ($isValid) {
                // Further validation by attempting to decode the address
                // This will throw an exception if the address is invalid
                $decoded = $this->cryptonote->decode_address($request->monero_address);
                // If we get here, the address is valid
                $isValid = true;
            }
        } catch (\Exception $e) {
            Log::error('Monero address validation error: ' . $e->getMessage());
            $isValid = false;
        }

        if ($isValid) {
            $user = Auth::user();

            // Check if the user has reached the limit of 8 return addresses.
            if ($user->returnAddresses()->count() >= 8) {
                return redirect()->route('return-addresses.index')
                    ->with('error', 'You can add a maximum of 8 return addresses.');
            }

            ReturnAddress::create([
                'user_id' => $user->id,
                'monero_address' => $request->monero_address,
            ]);

            return redirect()->route('return-addresses.index')
                ->with('success', 'Return address successfully added.');
        }

        return redirect()->route('return-addresses.index')
            ->with('error', 'Invalid Monero address. Please try again.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnAddress $returnAddress)
    {
        // Check if the return address belongs to the authenticated user.
        if ($returnAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $returnAddress->delete();

        return redirect()->route('return-addresses.index')
            ->with('success', 'Return address successfully deleted.');
    }
}

