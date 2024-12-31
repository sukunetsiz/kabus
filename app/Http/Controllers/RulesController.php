<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RulesController extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);
        $totalPages = 5;

        $paginatedRules = new LengthAwarePaginator(
            [], // Empty items array since content is in blade
            $totalPages,
            1,
            $page,
            ['path' => route('rules')]
        );

        return view('rules', compact('paginatedRules'));
    }
}