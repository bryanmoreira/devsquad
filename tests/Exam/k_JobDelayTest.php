<?php

declare(strict_types=1);

namespace Tests\Exam;

use App\Jobs\SaveRandomQuote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Http, Queue};
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Job Delay Test
 * - On this test we will check if you know how to:
 *
 * 1. Dispatch a job to the queue with a delay
 */
class k_JobDelayTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['queue.default' => 'database']);

        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    /**
     * Create a route that will dispacth the job
     * using the post method and name the route as
     * random-quote.store
     *
     * After, you should dispatch SaveRandomQuote job
     * with a twenty-minute delay
     *
     * Remember, to access this route, the user must be
     * authenticated
     */
    #[Test]
    public function it_should_ensure_that_the_job_will_be_dispatched_with_a_twenty_minute_delay(): void
    {
        Queue::fake();

        Carbon::setTestNow(now());

        $this->post(route('random-quote.store'))
            ->assertOk();

        Queue::assertPushed(
            fn (SaveRandomQuote $job) => now()->addMinutes(20)->equalTo($job->delay)
                && $this->user->is($job->user)
        );
    }

    /**
     * Now, we ensure that the delay will work
     */
    #[Test]
    public function it_should_ensure_that_the_job_will_be_executed_after_delay(): void
    {
        Http::fake([
            'https://api.quotable.io/random' => Http::response(['content' => 'Smile, breathe, and go slowly.']),
        ]);

        $this->post(route('random-quote.store'));

        $this->artisan('queue:work', ['--once' => true]);

        $this->assertDatabaseMissing('daily_logs', [
            'user_id' => $this->user->id,
            'log'     => 'Smile, breathe, and go slowly.',
        ]);

        $this->travel(21)->minutes();

        $this->artisan('queue:work', ['--once' => true]);

        $this->assertDatabaseHas('daily_logs', [
            'user_id' => $this->user->id,
            'log'     => 'Smile, breathe, and go slowly.',
        ]);
    }
}
