<?php

declare(strict_types=1);

namespace Tests\Exam;

use App\Mail\InviteUser;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * j_UserInvitationTest
 * - On this test we will check if you know how to:
 *
 * 1. Analyze a working feature and develop tests for it
 *
 * To develop your tests, you must take a look on the following files
 * - app/Http/Controllers/InvitationController.php
 * - app/Requests/Invitation
 * - app/Mail/InviteUser.php
 * - app/Models/Invitation.php
 * - routes/web.php
 */
class j_UserInvitationTest extends TestCase
{
    #[Test]
    public function it_should_allow_access_to_invite_only_for_logged_users(): void
    {
        $response = $this->postJson(route('invitations.store'), []);
        $response->assertStatus(401);

        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'email' => 'invitee@example.com',
        ];

        $response = $this->postJson(route('invitations.store'), $data);
        $response->assertStatus(201);
    }

    #[Test]
    public function it_should_check_if_email_is_filled_for_the_new_invitation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('invitations.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_should_check_if_email_address_is_valid_for_the_new_invitation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('invitations.store'), [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_should_check_email_size_for_new_invitation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $longEmail = str_repeat('a', 61) . '@example.com';

        $response = $this->postJson(route('invitations.store'), [
            'email' => $longEmail,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_should_create_an_invitation_and_send_it_to_user()
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $email = 'test@example.com';

        $response = $this->postJson(route('invitations.store'), [
            'email' => $email,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('invitations', ['email' => $email]);

        Mail::assertSent(InviteUser::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    #[Test]
    public function it_should_deny_invitation_acceptance_if_invitation_has_been_expired(): void
    {
        $invitation = Invitation::factory()->create(['expires_at' => now()->subHour()]);

        $response = $this->postJson(route('invitations.accept', $invitation), [
            'email' => $invitation->email,
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function should_deny_invitation_acceptance_if_invitation_has_already_been_accepted(): void
    {
        $invitation = Invitation::factory()->create(['activated_at' => now()]);

        $response = $this->postJson(route('invitations.accept', $invitation), [
            'email' => $invitation->email,
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_should_check_if_email_provided_matches_with_invitation_email(): void
    {
        $invitation = Invitation::factory()->create();

        $response = $this->postJson(route('invitations.accept', $invitation), [
            'email' => 'wrong@example.com',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_should_check_if_email_already_exists_on_users_table(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $invitation = Invitation::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $data = [
            'email' => 'existing@example.com',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->actingAs($existingUser);
        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function it_it_should_ensure_that_email_is_filled_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => '',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function it_should_ensure_that_email_address_is_valid_for_registrations(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'invalid-email',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function it_should_check_email_length_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => str_repeat('a', 61) . '@example.com',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function it_should_ensure_that_name_is_filled_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => '',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function it_should_ensure_that_name_is_a_string_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 12345,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function it_should_check_name_min_length_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Jo',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function it_should_validate_name_max_length_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => str_repeat('a', 46),
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function it_should_ensure_that_password_is_filled_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_ensure_that_password_was_confirmed_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password1234!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_check_if_password_has_at_least_eight_chars_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'Short1',
            'password_confirmation' => 'Short1',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_ensure_that_password_has_symbols_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_ensure_that_password_has_letters_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => '12345678!',
            'password_confirmation' => '12345678!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_ensure_that_password_has_numbers_for_registration(): void
    {
        $invitation = Invitation::factory()->create();

        $data = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'Password!',
            'password_confirmation' => 'Password!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_should_accept_the_invitation_and_create_a_new_user(): void
    {
        $invitation = Invitation::factory()->create([
            'email' => 'newuser@example.com',
        ]);

        $data = [
            'email' => 'newuser@example.com',
            'name' => 'New User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->postJson(route('invitations.accept', $invitation->code), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Your user have been created!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);

        $invitation->refresh();
        $this->assertNotNull($invitation->activated_at);
    }
}
