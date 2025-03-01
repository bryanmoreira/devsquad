<?php

namespace App\Http\Requests\Invitation;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read Invitation $invitation
 */
class AcceptInvite extends FormRequest
{
    public function authorize(): bool
    {
        return !($this->invitation->isExpired() || $this->invitation->isActivated());
    }

    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'email:rfc,filter',
                'max:60',
                Rule::in($this->invitation->email),
                Rule::unique('users'),
            ],
            'name'     => ['required', 'string', 'min:3', 'max:45'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->symbols()->letters()->numbers(),
            ],
        ];
    }
}
