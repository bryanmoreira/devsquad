<?php

namespace App\Listeners;

use App\Mail\DailyLogCopy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendDailyLogCopy
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        Mail::to($event->dailyLog->user->email)
            ->send(new DailyLogCopy($event->dailyLog));
    }
}
