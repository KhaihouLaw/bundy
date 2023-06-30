<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\User;
use App\Http\Traits\HasClockRecords;
use App\Utilities\CustomLogUtility;
use Exception;
use Log;
use Auth;

class TimesheetModificationRequest extends Model
{
    use HasFactory;
    use HasClockRecords;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'employee_id',
        'employee_schedule_id',
        'timesheet_id',
        'timesheet_date',
        'time_in',
        'time_out',
        'lunch_start',
        'lunch_end',
        'overtime_start',
        'overtime_end',
        'status',
        'notes',
        'reviewed_by'
    ];

    public function timesheet()
    {
        return $this->belongsTo(Timesheet::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->hasOne(User::class);
    }

    public function approve()
    {
        $this->status = static::STATUS_APPROVED;
        $this->reviewed_by = Auth::id();
        return $this->save();
    }

    public function reject()
    {
        $this->status = static::STATUS_REJECTED;
        $this->reviewed_by = Auth::id();
        return $this->save();
    }

    public function cancel()
    {
        $this->status = static::STATUS_CANCELLED;
        $this->reviewed_by = Auth::id();
        return $this->save();
    }

    public function isPending()
    {
        return ($this->status == static::STATUS_PENDING);
    }

    public function isApproved()
    {
        return ($this->status == static::STATUS_APPROVED);
    }

    public function isRejected()
    {
        return ($this->status == static::STATUS_REJECTED);
    }

    public function isCancelled()
    {
        return ($this->status == static::STATUS_CANCELLED);
    }

    public static function countPending()
    {
        return static::where('status', static::STATUS_PENDING)->count();
    }

    public static function countApproved()
    {
        return static::where('status', static::STATUS_APPROVED)->count();
    }

    public static function countRejected()
    {
        return static::where('status', static::STATUS_REJECTED)->count();
    }

    public static function countCancelled()
    {
        return static::where('status', static::STATUS_CANCELLED)->count();
    }

    public function getStatus($is_html = false)
    {
        if ($is_html) {
            if ($this->isPending()) {
                return "<label class='badge badge-warning'>{$this->status}</label>";
            }
            if ($this->isApproved()) {
                return "<label class='badge badge-success'>{$this->status}</label>";
            }
            if ($this->isRejected()) {
                return "<label class='badge badge-danger'>{$this->status}</label>";
            }
            if ($this->isCancelled()) {
                return "<label class='badge badge-secondary'>{$this->status}</label>";
            }
        }
        return $this->status;
    }

    public function hasNotes()
    {
        return (!empty($this->notes));
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public static function isModified($timesheet_id)
    {
        $modified = TimesheetModificationRequest::
            where('employee_id', Auth::user()->employee_id)->
            where('timesheet_id', $timesheet_id);
        if ($modified->count() == 0) return false;
        return true;
    }

    public function getStatusColor() {
        if ($this->isPending()) return '#c4c41f';
        if ($this->isApproved()) return 'green';
        return 'red'; // cancelled or rejected
    }

    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

    public static function getByDeparment($department_id)
    {
        $all_timesheet_adjustments = self::all();
        $department_timesheet_adjustments = [];
        foreach ($all_timesheet_adjustments as $adjustment) {
            $timesheet_dept_id = $adjustment->employee->department->id;
            if ($timesheet_dept_id === (int)$department_id) {
                array_push($department_timesheet_adjustments, $adjustment);
            }
        }
        return $department_timesheet_adjustments;
    }

    public function getStatusBadge($option = 1, $font_size = '14px') {
        if ($option === 1) {
            if ($this->isPending()) return '<span class="shadow-sm badge rounded-pill bg-warning bg-gradient ms-auto" style="font-size: ' . $font_size . '">Pending</span>';
            if ($this->isApproved()) return '<span class="shadow-sm badge rounded-pill bg-success bg-gradient ms-auto" style="font-size: ' . $font_size . '">Approved</span>';
            if ($this->isCancelled()) return '<span class="shadow-sm badge rounded-pill bg-secondary bg-gradient ms-auto" style="font-size: ' . $font_size . '">Cancelled</span>';
            return '<span class="shadow-sm badge rounded-pill bg-danger bg-gradient ms-auto" style="font-size: ' . $font_size . '">Rejected</span>';
        } elseif ($option == 2) {
            $class  = 'badge badge-pill text-white shadow py-2';
            if ($this->isPending()) return "<span class='{$class} badge-warning bg-gradient-warning' style='font-size: {$font_size} !important;'>{$this->status}</span>";
            if ($this->isApproved()) return "<span class='{$class} badge-success bg-gradient-success' style='font-size: {$font_size} !important;'>{$this->status}</span>";
            if ($this->isRejected()) return "<span class='{$class} badge-danger bg-gradient-danger' style='font-size: {$font_size} !important;'>{$this->status}</span>";
            if ($this->isCancelled()) return "<span class='{$class} badge-dark' style='font-size: {$font_size} !important;'>{$this->status}</span>";
        }
        // add more options here
        return null;
    }

    public static function deptPendingCount($department_id)
    {
        $all_pending_adjustments = self::where('status', self::STATUS_PENDING)->get();
        $dept_pending_count = 0;
        foreach ($all_pending_adjustments as $adjustment) {
            $timesheet_dept_id = $adjustment->employee->department->id;
            if ($timesheet_dept_id === (int)$department_id) {
                $dept_pending_count++;
            }
        }
        return $dept_pending_count;
    }

    /**
     * =================================================================================================================================
     * November 12, 2021
     * =================================================================================================================================
     */

    public static function pendingCount()
    {
        $all_pending = self::where('status', self::STATUS_PENDING)->get()->toArray();
        return count($all_pending);
    }
}
