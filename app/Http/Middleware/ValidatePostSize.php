<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidatePostSize as Middleware;

class ValidatePostSize extends Middleware
{
    /**
     * The maximum allowed POST data size.
     *
     * @var int
     */
    protected $max = null;
}
