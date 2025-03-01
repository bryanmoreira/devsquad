<?php

declare(strict_types = 1);

namespace Tests\Exam;

use App\Jobs\SaveRandomQuote;
use App\Models\DailyLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Command Test
 * - On this test we will check if you know how to:
 *
 * 1. Create an artisan command
 * 2. Create arguments for the command
 * 3. Return output messages
 */
class i_CommandTest extends TestCase
{
    /**
     * Create the command class
     */
    #[Test]
    public function create_command(): void
    {
        $this->assertTrue(
            class_exists('App\\Console\\Commands\\CreateDailyLog')
        );
    }

    /**
     * The command should receive the user id and should
     * validate if exists on database.
     *
     * It also should receive a date and validate if it's valid
     */
    #[Test]
    public function it_should_validate_input(): void
    {
        $this->artisan('daily-log', [
            'user' => User::max('id') + 1,
            'date' => now(),
        ])
            ->expectsOutput('The user that you want to retrieve hasn\'t been found on database.')
            ->assertFailed();

        $this->artisan('daily-log', [
            'user' => User::factory()->create()->id,
            'date' => '2022.01.01',
        ])
            ->expectsOutput('Please, provide a valid date.')
            ->assertFailed();
    }

    /**
     * After validating data, the command should dispatch the job
     * that you have created on test e_JobTest
     */
    #[Test]
    public function it_should_check_if_job_will_be_dispatched(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $date = '2022-07-19';

        $this->artisan('daily-log', [
            'user' => $user->id,
            'date' => $date,
        ])
            ->expectsOutput('Daily log created successfully!')
            ->assertSuccessful();

        Queue::assertPushed(
            SaveRandomQuote::class,
            fn (SaveRandomQuote $job) => $user->is($job->user)
                && Carbon::parse($date)->equalTo($job->date)
        );
    }

    /**
     * Making sure that the command is doing what supposed to do
     */
    #[Test]
    public function it_should_ensure_that_the_command_works(): void
    {
        Http::fake([
            'https://api.quotable.io/random' => Http::response([
                'content' => 'Walk as if you are kissing the Earth with your feet.',
                'author'  => 'Thich Nhat Hanh',
                'length'  => 52,
            ]),
        ]);

        $user = User::factory()->create();
        $date = '2022-07-19';

        $this->artisan('daily-log', [
            'user' => $user->id,
            'date' => $date,
        ])
            ->expectsOutput('Daily log created successfully!')
            ->assertSuccessful();

        $this->assertDatabaseHas(DailyLog::class, [
            'user_id' => $user->id,
            'log'     => 'Walk as if you are kissing the Earth with your feet.',
            'day'     => Carbon::parse($date)->format('Y-m-d H:i:s'),
        ]);
    }
}
