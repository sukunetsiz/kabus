<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\HandleCors as Middleware;

class HandleCors extends Middleware
{
    /**
     * The names of headers that should be added to the CORS response.
     *
     * @var array<int, string>
     */
    protected $addedHeaders = [];

    /**
     * The names of headers that are allowed for CORS requests.
     *
     * @var array<int, string>
     */
    protected $allowedHeaders = ['*'];

    /**
     * The names of methods that are allowed for CORS requests.
     *
     * @var array<int, string>
     */
    protected $allowedMethods = ['*'];

    /**
     * The URIs that should be allowed to access the resource.
     *
     * @var array<int, string>
     */
    protected $allowedOrigins = ['*'];

    /**
     * Indicates whether or not the CORS response should allow credentials.
     *
     * @var bool
     */
    protected $supportsCredentials = false;
}
