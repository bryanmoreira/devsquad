<?php

declare(strict_types = 1);

namespace Tests\Exam;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Upload File Test
 * - On this test we will check if you know how to:
 *
 * 1. Validate the file input
 * 2. Upload a file to a specific disk
 *
 * @package Tests\Exam
 */
class g_FileUploadTest extends TestCase
{
    /**
     * Create route that will upload the avatar
     * using the put method and name the route as
     * profile.update-avatar
     */
    #[Test]
    public function create_route(): void
    {
        Storage::fake('public');

        $user = \App\Models\User::factory()->create([
            'avatar' => null,
        ]);

        $this->actingAs($user)->put(route('profile.update-avatar'), [
            'avatar' => UploadedFile::fake()->image('my-avatar.jpg'),
        ]);

        $user->refresh();

        $this->assertDatabaseMissing('users', [
            'id'     => $user->id,
            'avatar' => null,
        ]);

        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Validates the payload
     * - avatar: should be required
     *           should be a successfully uploaded file
     *           should be an image
     *           Its size shouldn't be greater than 1MB
     */
    #[Test, DataProvider('uploadValidationData')]
    public function it_should_validates_the_payload(mixed $avatar, string $errorMessage): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.update-avatar'), ['avatar' => $avatar])
            ->assertSessionHasErrors(['avatar' => $errorMessage]);
    }

    public static function uploadValidationData(): array
    {
        return [
            [null, 'The avatar field is required.'],
            ['any content here', 'The avatar must be a file.'],
            [self::createFakeFile('avatar.csv', 100, 'text/csv'), 'The avatar must be an image.'],
            [self::createFakeFile('avatar.jpg', 1500, 'image/jpg'), 'The avatar must not be greater than 1024 kilobytes.'],
        ];
    }

    private static function createFakeFile(
        string $name,
        int $size,
        string $mimeType,
    ): UploadedFile {
        return UploadedFile::fake()->create($name, $size, $mimeType);
    }
}
