<?php

namespace App\Jobs;

use App\Models\DailyLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaveRandomQuote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public Carbon $date)
    {
    }

    public function middleware(): array
    {
        return [new RateLimited('user-rate-limit')];
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::info('SaveRandomQuote job is running!!! 🧨');

        $response = Http::get('https://api.quotable.io/random');

        if (!$response->successful()) {
            Log::error('Failed to get a quote from the API');
            throw new \Exception('Failed to fetch quote');
        }

        $quote = $response->json('content');

        Log::info("Quote received: {$quote}");

        DailyLog::create([
            'user_id' => $this->user->id,
            'day' => $this->date,
            'log' => $quote,
        ]);
    }
}
