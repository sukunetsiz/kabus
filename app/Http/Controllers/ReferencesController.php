<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ReferencesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $referenceId = $user->reference_id;
        
        return view('references', compact('referenceId'));
    }
}
