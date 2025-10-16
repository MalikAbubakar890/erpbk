<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\GlobalPagination;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:activity_logs_view')->only(['index', 'show']);
        $this->middleware('permission:activity_logs_delete')->only(['destroy']);
    }
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        if ($request->filled('module_name')) {
            $query->forModule($request->module_name);
        }

        if ($request->filled('action')) {
            $query->forAction($request->action);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        // Get unique values for filter dropdowns
        $users = User::select('id', 'name')->orderBy('name')->get();
        $modules = ActivityLog::select('module_name')
            ->distinct()
            ->orderBy('module_name')
            ->pluck('module_name');

        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Paginate results
        $activityLogs = $query->orderBy('id', 'asc')->paginate(50);

        return view('activity_logs.index', compact(
            'activityLogs',
            'users',
            'modules',
            'actions'
        ));
    }

    /**
     * Show the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('activity_logs.show', compact('activityLog'));
    }

    /**
     * Get activity log statistics.
     */
    public function statistics(Request $request)
    {
        $query = ActivityLog::query();

        // Apply date filter if provided
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        $stats = [
            'total_activities' => (clone $query)->count(),

            'activities_by_action' => (clone $query)
                ->select('action', DB::raw('COUNT(*) as count'))
                ->groupBy('action')
                ->orderByDesc('count')
                ->get(),

            'activities_by_module' => (clone $query)
                ->select('module_name', DB::raw('COUNT(*) as count'))
                ->groupBy('module_name')
                ->orderByDesc('count')
                ->get(),

            'activities_by_user' => (clone $query)
                ->with('user')
                ->select('user_id', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_activity'))
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),

            'recent_activities' => (clone $query)
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    $activity->user_name = $activity->user?->name ?? 'System';
                    return $activity;
                }),
        ];


        return response()->json($stats);
    }
}
