<?php

declare(strict_types=1);

namespace Tests\Exam;

use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Package Installation test
 * - On this test your will require to install and
 *   set up the package http://www.laravel-auditing.com
 *
 * 1. Install the package
 * 2. Make sure that is working on a User Model
 *
 * @package Tests\Exam
 */
class d_PackageInstallationTest extends TestCase
{
    /**
     * Install Laravel Auditing Package
     */
    #[Test]
    public function install_a_package(): void
    {
        $output = Process::run(
            command: 'composer show'
        )->output();

        $this->assertTrue(str_contains($output, 'owen-it/laravel-auditing'));
    }

    /**
     * Set up a package following the documentation
     * - Activate the package for User Model
     * ----------------------------------------------------------------------
     * For this test, make sure that you change the following configuration:
     * audit.console = true;
     */
    #[Test]
    public function setup_laravel_audition_package(): void
    {
        config(['audit.console' => true]);

        $user       = \App\Models\User::factory()->create();
        $user->name = 'Joe Doe';
        $user->save();

        $this->assertCount(2, $user->audits);
    }
}
