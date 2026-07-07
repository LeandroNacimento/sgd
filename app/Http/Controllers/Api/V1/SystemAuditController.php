<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Activitylog\Models\Activity;

class SystemAuditController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ActivityResource::collection($activities);
    }
}
