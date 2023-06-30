<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utilities\EmailUtility;

class DailyClockOutReminderEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily-clock-out-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send clock out reminder email to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        EmailUtility::sendDailyClockOutEmail();
        return 0;
    }
}
