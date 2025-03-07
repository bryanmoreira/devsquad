<?php

declare(strict_types=1);

namespace Tests\Exam;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Models Test
 * - On this test we will check if you know how to:
 *
 * 1. Create a model
 * 2. Work with relationships
 * 3. Create Model Factories
 * 4. Implement query scopes
 * 5. Use Get Mutators
 * 6. Use Set Mutators
 * 7. Casting properties
 * 8. Notification
 *
 * @package Tests\Exam
 */
class b_ModelTest extends TestCase
{
    /**
     * Create DailyLog Model
     */
    #[Test]
    public function create_a_model(): void
    {
        $this->assertTrue(
            class_exists('App\Models\DailyLog')
        );
    }

    /**
     * Create relationships between User and DailyLogs
     */
    #[Test]
    public function relationship_with_user(): void
    {
        $dailyLog     = new \App\Models\DailyLog();
        $relationship = $dailyLog->user();

        $this->assertInstanceOf(
            BelongsTo::class,
            $relationship,
            'dailyLogs->user()'
        );

        $dailyLog     = new \App\Models\User();
        $relationship = $dailyLog->dailyLogs();

        $this->assertInstanceOf(
            HasMany::class,
            $relationship,
            'user->dailyLogs()'
        );
    }

    /**
     * Create factories for User and DailyLog
     */
    #[Test]
    public function create_factories(): void
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\DailyLog::factory()->create(['user_id' => $user->id]);

        $this->assertCount(1, \App\Models\DailyLog::all());
    }

    /**
     * Implement Model Query Scope to filter DailyLog for today
     */
    #[Test]
    public function implement_query_scope(): void
    {
        $user = \App\Models\User::factory()->create();

        \App\Models\DailyLog::factory()->count(3)->create([
            'day'     => now()->subDay(),
            'user_id' => $user->id,
        ]);

        \App\Models\DailyLog::factory()->count(3)->create([
            'day'     => now(),
            'user_id' => $user->id,
        ]);

        $todaysLog = $user->dailyLogs()->fromToday()->get();

        $this->assertCount(3, $todaysLog);
    }

    /**
     * Create a get mutator on User's model to transform
     * the return from "joe doe" to "Joe Doe"
     */
    #[Test]
    public function use_get_mutator(): void
    {
        $user = \App\Models\User::factory()->make();

        $user->name = 'joe doe';

        $this->assertEquals('Joe Doe', $user->name);
    }

    /**
     * Create a set mutator on User's model to transform
     * the password to a hash string when setting the password
     */
    #[Test]
    public function use_set_mutator(): void
    {
        $user = \App\Models\User::factory()->make();

        $user->password = 'secret';

        $this->assertTrue(
            Hash::check('secret', $user->password)
        );
    }

    /**
     * When retrieving the day from a DailyLog it should return
     * an instance of Carbon
     */
    #[Test]
    public function it_should_ensure_that_date_is_a_carbon_instance(): void
    {
        $dailyLog = \App\Models\DailyLog::factory()->make([
            'day' => '2020-02-02',
        ]);

        $this->assertInstanceOf(Carbon::class, $dailyLog->day);
    }

    /**
     * When we update a password of a user the same user should be
     * notified by email that his password was changed.
     */
    #[Test]
    public function it_should_notify_the_user_after_a_password_change(): void
    {
        Notification::fake();

        $user = \App\Models\User::factory()->create();

        $user->password = 'secret';
        $user->save();

        Notification::assertSentTo($user, \App\Notifications\PasswordChanged::class);
    }
}
