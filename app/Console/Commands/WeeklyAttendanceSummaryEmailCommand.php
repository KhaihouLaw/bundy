<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utilities\EmailUtility;

class WeeklyAttendanceSummaryEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:weekly-attendance-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly attendance email to HR and Supervisors';

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
        EmailUtility::sendWeeklyAttendanceSummaryEmail();
        return 0;
    }
}
