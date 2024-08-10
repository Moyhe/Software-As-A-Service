<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function __invoke()
    {
        $fatures = Feature::query()->where('active', true)->get();

        return inertia('Welcome', [
            'features' => FeatureResource::collection($fatures),
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register')
        ]);
    }
}
