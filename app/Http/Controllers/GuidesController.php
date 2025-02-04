<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuidesController extends Controller
{
    public function index()
    {
        return view('guides.index');
    }

    public function keepassxc()
    {
        return view('guides.keepassxc-guide');
    }

    public function monero()
    {
        return view('guides.monero-guide');
    }

    public function tor()
    {
        return view('guides.tor-guide');
    }
}
