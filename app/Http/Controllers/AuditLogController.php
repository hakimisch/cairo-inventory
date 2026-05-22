<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a paginated, searchable list of all activity log entries.
     */
    public function index(Request $request)
    {
        $query = Activity::with('causer')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                  ->orWhere('event', 'ILIKE', "%{$search}%")
                  ->orWhere('subject_type', 'ILIKE', "%{$search}%")
                  ->orWhere('log_name', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->event) {
            $query->where('event', $request->event);
        }

        $activities = $query->paginate(30)
            ->withQueryString()
            ->through(function ($activity) {
                return [
                    'id'          => $activity->id,
                    'log_name'    => $activity->log_name,
                    'description' => $activity->description,
                    'event'       => $activity->event,
                    'subject_type' => class_basename($activity->subject_type ?? ''),
                    'subject_id'  => $activity->subject_id,
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'properties'  => $activity->properties->toArray(),
                    'created_at'  => $activity->created_at?->toISOString(),
                ];
            });

        return inertia('AuditLog/Index', [
            'activities' => $activities,
            'filters'    => $request->only(['search', 'event']),
            'events'     => ['created', 'updated', 'deleted', 'restored'],
        ]);
    }
}
