<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteUser extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Invitation $invitation)
    {
        //
    }

    public function build(): InviteUser
    {
        return $this->subject('You\'re invited!')
            ->view('emails.invite-user');
    }
}
