<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\LeaveRequestNotif;

class ApprovedLeaveRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaveRequest:approved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification for approved leave request';

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
        $user = User::where('email', env('BUNDY_EMAIL'))->first();
        $details = [
            'subject' => 'Your Leave Request is Approved [#LEAVE_REQUEST_ID]',
            'greetings' => 'Leave Request',
            'id' => 'LEAVE_REQUEST_ID',
            'from' => $user->name,
            'date' => 'DATE_FROM to DATE_TO',
            'days' => 'Number of days',
            'type' => '["Sick Leave" / "Vacation Leave"]',
            'description' => 'LEAVE REQUEST DESCRIPTION',
            'date-request' => 'DATE_REQUESTED',
        ];
        $user->notify(new LeaveRequestNotif($details));
        $this->info("Approved leave request notification sent to " . $user->email);
        return 0;
    }
}
