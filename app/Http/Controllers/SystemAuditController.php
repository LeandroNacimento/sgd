<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class SystemAuditController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', compact('activities'));
    }
}
