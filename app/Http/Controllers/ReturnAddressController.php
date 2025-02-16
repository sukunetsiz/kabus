<?php

namespace App\Http\Controllers;

use App\Models\ReturnAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReturnAddressController extends Controller
{
    /**
     * The regex pattern for validating Monero addresses.
     */
    private const MONERO_ADDRESS_REGEX = "/^(4[1-9AB][1-9A-HJ-NP-Za-km-z]{93}|8[2-9ABC][1-9A-HJ-NP-Za-km-z]{93})$/";

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
        $request->validate([
            'monero_address' => [
                'required',
                'string',
                'max:255',
                Rule::unique('return_addresses')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ]);

        // Validate the address using regex.
        $isValid = preg_match(self::MONERO_ADDRESS_REGEX, $request->monero_address) === 1;

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

