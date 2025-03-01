<?php

namespace App\Http\Requests\Invitation;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'max:60', 'email:rfc,filter'],
        ];
    }
}
