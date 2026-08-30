<?php

namespace App\Http\Controllers;

use App\Enums\AssetCategory;
use Inertia\Inertia;
use Inertia\Response;

class PublicLandingController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Public/Landing', [
            'heroTitle' => 'Request the right equipment for your team',
            'heroDescription' => 'Share your equipment needs with our backoffice team and we will follow up with availability and next steps.',
            'quoteSubmitUrl' => route('quote-requests.store', absolute: false),
            'backofficeUrl' => route('login', absolute: false),
            'assetCategories' => array_map(static fn (AssetCategory $category): array => [
                'value' => $category->value,
                'label' => str($category->value)->replace('_', ' ')->title()->toString(),
            ], AssetCategory::cases()),
        ]);
    }
}
