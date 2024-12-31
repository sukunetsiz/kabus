<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonetaController extends Controller
{
    public function index()
    {
        return view('moneta.index');
    }
}