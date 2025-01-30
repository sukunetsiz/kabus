<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $popup = null;
        // Only show popup if user hasn't dismissed it in this session
        if (!session()->has('popup_dismissed')) {
            $popup = \App\Models\Popup::getActive();
        }
        return view('home', [
            'username' => Auth::user()->username,
            'popup' => $popup
        ]);
    }

    /**
     * Dismiss the popup for the current session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dismissPopup(Request $request)
    {
        if ($request->has('dismiss_popup')) {
            session(['popup_dismissed' => true]);
        }
        return redirect()->route('home');
    }
}