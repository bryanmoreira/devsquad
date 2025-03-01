<?php

namespace App\Http\Controllers;

use App\Events\DailyLogCreated;
use App\Http\Requests\StoreDailyLogRequest;
use App\Http\Requests\UpdateDailyLogRequest;
use App\Models\DailyLog;
use Illuminate\Http\JsonResponse;

class DailyLogController extends Controller
{

    public function store(StoreDailyLogRequest $request): JsonResponse
    {
        $dailyLog = DailyLog::create([
            'user_id' => auth()->id(),
            'day' => $request->input('day'),
            'log' => $request->input('log'),
        ]);

        event(new DailyLogCreated($dailyLog));

        return response()->json($dailyLog, 201);
    }


    public function update(UpdateDailyLogRequest $request, DailyLog $dailyLog): JsonResponse
    {
        $dailyLog->update($request->validated());

        return response()->json($dailyLog);
    }

    public function destroy(DailyLog $dailyLog): JsonResponse
    {
        $this->authorize('delete', $dailyLog);

        $dailyLog->delete();

        return response()->json(null, 204);
    }
}
