<?php

namespace App\Policies;

use App\Models\DailyLog;
use App\Models\User;

class DailyLogPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, DailyLog $dailyLog): bool
    {
        return $user->id === $dailyLog->user_id;
    }
}
