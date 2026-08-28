<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PublicLandingController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Public/Landing', [
            'ctaUrl' => route('dashboard', absolute: false),
        ]);
    }
}
