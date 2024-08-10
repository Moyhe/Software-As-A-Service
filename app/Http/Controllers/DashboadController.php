<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsedFeatureResource;
use App\Models\UsedFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboadController extends Controller
{
    public function __invoke()
    {
        $usedFeatures = UsedFeature::query()
            ->where('user_id', Auth::id())
            ->with('feature')->latest()->paginate(5);

        return inertia('Dashboard', [
            'usedFeatures' => UsedFeatureResource::collection($usedFeatures)
        ]);
    }
}
