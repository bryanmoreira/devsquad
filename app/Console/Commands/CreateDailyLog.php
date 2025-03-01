<?php

namespace App\Console\Commands;

use App\Jobs\SaveRandomQuote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateDailyLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-log {user} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a daily log for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $date = $this->argument('date');

        $user = User::find($userId);

        if (!$user) {
            $this->error('The user that you want to retrieve hasn\'t been found on database.');
            return self::FAILURE;
        }

        $validator = Validator::make(['date' => $date], ['date' => 'date']);
        if ($validator->fails()) {
            $this->error("Please, provide a valid date.");
            return self::FAILURE;
        }

        // Dispatch the job
        SaveRandomQuote::dispatch($user, Carbon::parse($date));
        $this->info("Daily log created successfully!");

        return self::SUCCESS;
    }
}
