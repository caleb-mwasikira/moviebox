<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $filterUser = $request->input('user_id');

        // Per-user summary: pivot action × subject_type counts
        $rawStats = ActivityLog::selectRaw('user_id, user_name, action, subject_type, count(*) as total')
            ->groupBy('user_id', 'user_name', 'action', 'subject_type')
            ->orderBy('user_name')
            ->get();

        // Build a keyed summary: [user_id => ['name' => ..., 'counts' => [action_subjecttype => n]]]
        $summary = [];
        foreach ($rawStats as $row) {
            $uid = $row->user_id ?? 'deleted_' . $row->user_name;
            if (!isset($summary[$uid])) {
                $summary[$uid] = ['name' => $row->user_name, 'counts' => []];
            }
            $key = $row->action . '|' . $row->subject_type;
            $summary[$uid]['counts'][$key] = $row->total;
        }

        // Recent feed
        $query = ActivityLog::with('user')->latest('created_at');
        if ($filterUser) {
            $query->where('user_id', $filterUser);
        }
        $recent = $query->paginate(50)->withQueryString();

        // Staff users for filter dropdown
        $staffUsers = User::whereNotNull('id')
            ->whereIn('id', ActivityLog::select('user_id')->whereNotNull('user_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $columns = [
            'created|Movie'       => 'Movies Created',
            'updated|Movie'       => 'Movies Updated',
            'deleted|Movie'       => 'Movies Deleted',
            'force_deleted|Movie' => 'Movies Purged',
            'restored|Movie'      => 'Movies Restored',
            'created|Series'      => 'Series Created',
            'updated|Series'      => 'Series Updated',
            'deleted|Series'      => 'Series Deleted',
            'force_deleted|Series'=> 'Series Purged',
            'restored|Series'     => 'Series Restored',
            'created|Episode'     => 'Episodes Added',
            'deleted|Episode'     => 'Episodes Deleted',
        ];

        return view('pages.admin.activity.index', compact('summary', 'recent', 'staffUsers', 'filterUser', 'columns'));
    }
}
