<?php

namespace App\Actions\Exam;

use Illuminate\Support\Facades\Http;

class ConsumeAPI
{
    public static function execute(): array
    {
        $response = Http::post('https://ios-api.devsquad.app/oauth/token', [
            'grant_type' => 'password',
            'client_id' => '3',
            'client_secret' => 'Fql3okYQbbzDtlmhBXdLE2eWy3OR9MR9x3n9NwqL',
            'username' => 'joe@doe.com',
            'password' => 'secret',
            'scope' => '*',
        ]);

        $token = $response->json('access_token');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://ios-api.devsquad.app/api/me');

        return $response->json();
    }

}
