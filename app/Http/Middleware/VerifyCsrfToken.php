<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/offert/payment/bono',
        '/changeActiveYear',
        'stripe/*',
        'api/*',
        'stripe-events/*',
        'admin/clientes/autosaveNutri',
        'admin/clientes/autosaveValora',
        'admin/clientes/sendFileTo',
        'admin/autosaveClinicHist',
        'admin/autosaveClinicHistSPelv',
    ];
}
