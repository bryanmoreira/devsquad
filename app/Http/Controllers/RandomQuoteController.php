<?php

namespace App\Http\Controllers;

use App\Jobs\SaveRandomQuote;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RandomQuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        SaveRandomQuote::dispatch($user, Carbon::now())->delay(now()->addMinutes(20));

        return response()->json(['message' => 'Job dispatched with a twenty-minute delay'], 200);
    }
}
