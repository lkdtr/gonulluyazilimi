<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProcessLogs;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcessLogController extends Controller
{
    public function getList(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q')),
            'type' => $request->query('type'),
            'subject' => $request->query('subject'),
            'subject_id' => $request->integer('subject_id') ?: null,
            'by' => $request->integer('by') ?: null,
            'from' => $request->date('from'),
            'to' => $request->date('to'),
        ];

        $logs = ProcessLogs::query()
            ->when($filters['q'] !== '', fn ($query) => $query->where('process', 'like', "%{$filters['q']}%"))
            ->when(in_array($filters['type'], ['create', 'change', 'delete', 'other'], true), fn ($query) => $query->where('process_type', $filters['type']))
            ->when(array_key_exists((string) $filters['subject'], Audit::SUBJECTS), fn ($query) => $query->where('subject_type', $filters['subject']))
            ->when($filters['subject_id'], fn ($query, $id) => $query->where('subject_id', $id))
            ->when($filters['by'], fn ($query, $by) => $query->where('process_by', $by))
            ->when($filters['from'], fn ($query, $from) => $query->where('created_at', '>=', $from->startOfDay()))
            ->when($filters['to'], fn ($query, $to) => $query->where('created_at', '<=', $to->endOfDay()))
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('admin::process_logs', [
            'logs' => $logs,
            'users' => User::whereIn('id', $logs->pluck('process_by')->filter()->unique())->get()->keyBy('id'),
            'filters' => $filters,
            'byUser' => $filters['by'] ? User::find($filters['by']) : null,
        ]);
    }
}
