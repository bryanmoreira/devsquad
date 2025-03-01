<?php

declare(strict_types=1);

namespace Tests\Exam;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * HTTP Test
 * - On this test we will check if you know how to:
 *
 * 1. Consume an api
 * 2. Organize your code moving information to a config file
 *
 * @package Tests\Exam
 */
class f_HttpTest extends TestCase
{
    /**
     * Implement the code inside \App\Actions\Exam\ConsumeAPI
     * to get the data from the API: https://ios-api.devsquad.app
     *
     * Endpoints:
     *  POST /oauth/token
     *          Body:   {
     *                      "grant_type" : "password",
     *                      "client_id" : "3",
     *                      "client_secret" : "Fql3okYQbbzDtlmhBXdLE2eWy3OR9MR9x3n9NwqL",
     *                      "username" : "joe@doe.com",
     *                      "password" : "secret",
     *                      "scope" : "*"
     *                  }
     *
     *  GET /api/me
     *  Header:
     *  - Authorization: Bearer Token {{ token from the previous endpoint }}
     *  - Content-Type: application/json
     *  - Accept: application/json
     */
    #[Test]
    public function it_should_consume_an_api(): void
    {
        $data = \App\Actions\Exam\ConsumeAPI::execute();

        $this->assertEquals([
            'id'     => 50,
            'name'   => 'Joe Doe',
            'email'  => 'joe@doe.com',
            'avatar' => null,
        ], $data);
    }

    /**
     * Use app config to organize your service
     */
    #[Test]
    public function it_should_use_config_to_organize_your_service(): void
    {
        $this->assertArrayHasKey('exam', config('services'));
        $this->assertArrayHasKey('endpoint', config('services.exam'));
        $this->assertEquals('https://ios-api.devsquad.app', config('services.exam.endpoint'));
    }
}
