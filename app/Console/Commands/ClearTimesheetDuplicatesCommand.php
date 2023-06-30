<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Timesheet;
use Illuminate\Console\Command;

class ClearTimesheetDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:clear-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear timesheet duplicates, only 1 timesheet is allowed for a specific date.';

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
        /**
         * Clear all timesheet duplicates in a specific date
         * Retains only 1 timesheet
         * Prioritizes to retain not empty (has time_in value) timesheets
         */

        ini_set('max_execution_time', 1800); // 30 mins

        try {
            $employees = Employee::all()->sortBy('last_name');
            foreach ($employees as $employee) {
                error_log(' > ' . $employee->getId() . ' ' . $employee->getFullName() . ' clearing timesheet duplicates...');

                $date_to_clear_duplicates = null;
                $remaining_timesheet = 0; // this should only be 1
                $selected_empty_timesheet_id = null;
                $duplicates_to_delete = [];
                $success_clearing = true;

                Timesheet::where('employee_id', $employee->id)
                            ->orderBy('timesheet_date', 'asc')
                            ->orderBy('id', 'asc')
                            ->chunk(10, function($timesheets) use (&$date_to_clear_duplicates, 
                                                                    &$remaining_timesheet, 
                                                                    &$selected_empty_timesheet_id,
                                                                    &$success_clearing, 
                                                                    &$duplicates_to_delete) {
                                foreach ($timesheets as $timesheet) {

                                    if (is_null($date_to_clear_duplicates)) {
                                        $date_to_clear_duplicates = $timesheet->timesheet_date;
                                    } 
                                    // no more duplicates in date
                                    elseif (
                                        !is_null($date_to_clear_duplicates) &&
                                        ($date_to_clear_duplicates !== $timesheet->timesheet_date)
                                    ) {
                                        $date_to_clear_duplicates = $timesheet->timesheet_date;
                                        $remaining_timesheet = 0;
                                        $selected_empty_timesheet_id = null;
                                    }
                
                                    // if time_in not empty
                                    // retain this timesheet, then delete the rest
                                    if (!empty($timesheet->time_in)) {
                                        if (!is_null($selected_empty_timesheet_id)) {
                                            array_push($duplicates_to_delete, $selected_empty_timesheet_id);
                                            $remaining_timesheet--;
                                        }
                                        $remaining_timesheet++;
                                    } 
                                    // when all timesheets (including duplicates) in specific date is empty
                                    // pick and retain only 1 timesheet
                                    elseif (
                                        empty($timesheet->time_in) &&
                                        ($remaining_timesheet === 0)
                                    ) {
                                        $remaining_timesheet++;
                                        $selected_empty_timesheet_id = $timesheet->id;
                                    }
                                    // deletes other empty duplicates
                                    elseif (
                                        empty($timesheet->time_in) &&
                                        ($remaining_timesheet !== 0) // there's already retained empty timesheet
                                    ) {
                                        array_push($duplicates_to_delete, $timesheet->id);
                                    }
                                    // something is wrong, for example: 2 or more duplicate timesheets has time_in 
                                    // these timesheets will not be deleted
                                    else {
                                        $success_clearing = false;
                                    }
                                }
                            });

                do {
                    $deleted = Timesheet::whereIn('id', $duplicates_to_delete)->limit(1000)->delete();
                    sleep(2);
                } while ($deleted > 0);
                
                if ($success_clearing) {
                    error_log(' >>> successful, all duplicates are cleared');
                } else {
                    error_log(' >>> Something is wrong');
                }
            }
        } catch (\Throwable $th) {
            error_log(' >>> An error occured');
        }
        return Command::SUCCESS;
    }
}
