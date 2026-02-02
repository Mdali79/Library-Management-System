<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'fines/payment/ipn',     // SSLCommerz IPN callback (no CSRF from gateway)
        'fines/payment/success', // Gateway redirects back with POST; no CSRF token
        'fines/payment/fail',
        'fines/payment/cancel',
        'fines/initiate-ssl/*',  // Student starts payment; auth + fine ownership validated in controller
    ];
}
