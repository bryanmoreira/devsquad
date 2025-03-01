<?php

declare(strict_types=1);

namespace Tests\Exam;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Job Test
 * - On this we will check if you know how to:
 *
 * 1. Create a Job
 * 2. Send the Job to the Queue
 *
 * @package Tests\Exam
 */
class e_JobTest extends TestCase
{
    /**
     * Create a job that will request for a date and a user
     * it will get a random quote from an api a save
     * as a Daily Log
     *
     * - Get a random quote from https://api.quotable.io/random
     *  ( you can check on ./tests/Fixtures/quotes.http )
     */
    #[Test]
    public function create_job(): void
    {
        Queue::fake();

        $user = \App\Models\User::factory()->create();

        $date = Carbon::parse('2020-01-01');

        \App\Jobs\SaveRandomQuote::dispatch($user, $date);

        Queue::assertPushed(\App\Jobs\SaveRandomQuote::class);
    }

    /**
     * Making sure that the job is doing
     * what supposed to do
     */
    #[Test]
    public function it_should_ensure_that_the_job_worked(): void
    {
        Http::fake([
            'https://api.quotable.io/random' => Http::response([
                '_id'     => '5U3Qdp9L0OId',
                'tags'    => [
                    'famous-quotes',
                ],
                'content' => 'Friends are those rare people who ask how we are and then wait to hear the answer.',
                'author'  => 'Ed Cunningham',
                'length'  => 82,
            ]),
        ]);

        $user = \App\Models\User::factory()->create();

        $date = Carbon::parse('2020-01-01');

        \App\Jobs\SaveRandomQuote::dispatch($user, $date);

        $this->assertDatabaseHas('daily_logs', [
            'user_id' => $user->id,
            'day'     => '2020-01-01 00:00:00',
            'log'     => 'Friends are those rare people who ask how we are and then wait to hear the answer.',
        ]);
    }

    /**
     * Let's add logs to the job
     */
    #[Test]
    public function add_logs_to_the_job_so_we_can_debug_later(): void
    {
        Log::spy();

        Http::fake([
            'https://api.quotable.io/random' => Http::response([
                '_id'     => '5U3Qdp9L0OId',
                'tags'    => [
                    'famous-quotes',
                ],
                'content' => 'Friends are those rare people who ask how we are and then wait to hear the answer.',
                'author'  => 'Ed Cunningham',
                'length'  => 82,
            ]),
        ]);

        $user = \App\Models\User::factory()->create();

        $date = Carbon::parse('2020-01-01');

        \App\Jobs\SaveRandomQuote::dispatch($user, $date);

        Log::shouldHaveReceived('info')
            ->with('SaveRandomQuote job is running!!! 🧨');
    }
}
