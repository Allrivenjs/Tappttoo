<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ReportProblem as ReportProblemModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportProblem extends Controller
{

    public function reportProblem(Request $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:' . implode(',', ReportProblemModel::TYPES),
            'post_id' => 'exists:posts,id',
            'user_id' => 'exists:users,id',
        ]);
        $payload = $request->toArray();
        $payload['user_id'] = $this->authApi()->id();
        $payload['payload'] = $request->all();
        ReportProblemModel::query()->create($payload);
        return response([
            'message' => 'Reported',
        ]);
    }

    public function markAsResolved(ReportProblemModel $reportProblem): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $reportProblem->update([
            'resolved_at' => Carbon::now(),
        ]);
        return response([
            'message' => 'Marked as resolved',
        ]);
    }

    public function getReportedProblems(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $reportedProblems = ReportProblemModel::query()
            ->with(['user', 'post'])
            ->where('resolved_at', '=', null)
            ->get();
        return response([
            'reportedProblems' => $reportedProblems,
        ]);
    }
}
