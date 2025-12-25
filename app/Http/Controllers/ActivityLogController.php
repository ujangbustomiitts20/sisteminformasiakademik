<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $logs = $query->paginate(50)->withQueryString();
        
        // Get unique actions for filter
        $actions = ActivityLog::select('action')
            ->distinct()
            ->pluck('action');
        
        // Get users for filter
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);
        
        return view('activity-log.index', compact('logs', 'actions', 'users'));
    }

    /**
     * Show activity log details
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        
        return view('activity-log.show', compact('log'));
    }

    /**
     * Clear old logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7'
        ]);
        
        $date = now()->subDays($request->days);
        
        $deleted = ActivityLog::where('created_at', '<', $date)->delete();
        
        ActivityLog::log('clear_logs', "Menghapus {$deleted} log aktivitas yang lebih dari {$request->days} hari");
        
        return redirect()->route('activity-log.index')
            ->with('success', "Berhasil menghapus {$deleted} log aktivitas.");
    }

    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->limit(10000)->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_log_' . date('Y-m-d_His') . '.csv"',
        ];

        $columns = ['ID', 'Waktu', 'User', 'Action', 'Deskripsi', 'Model', 'Model ID', 'IP Address'];

        $callback = function() use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : '-',
                    $log->action,
                    $log->description,
                    $log->model_type ? class_basename($log->model_type) : '-',
                    $log->model_id ?? '-',
                    $log->ip_address ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
