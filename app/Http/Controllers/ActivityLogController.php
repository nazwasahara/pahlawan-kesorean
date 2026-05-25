<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Unauthorized action.');
        }

        $date = $request->input('date');

        $query = ActivityLog::query()->orderBy('created_at', 'desc')->orderBy('id', 'desc');

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('admin-owner.logs.index', compact('logs', 'date'));
    }
}
