<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveType;
use App\Models\LeaveRequestLog;
use App\Models\User;
use App\Utilities\LVCCCrypto;
use App\Utilities\TimeUtility;
use Exception;

class LeaveRequest extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const REJECTED = 'rejected';
    const APPROVED = 'approved';
    const CANCELLED = 'cancelled';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'reason',
        'assigned_reviewer_id',
        'reviewed_by',
        'start_date',
        'end_date',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function reviewedBy()
    {
        return $this->hasOne(User::class);
    }

    public function assignedReviewer()
    {
        return $this->belongsTo(Employee::class, 'assigned_reviewer_id');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStartDate($format = null)
    {
        if (!empty($format)) {
            return date($format, strtotime($this->start_date));
        }
        return $this->start_date;
    }

    public function getEndDate($format = null)
    {
        if (!empty($format)) {
            return date($format, strtotime($this->end_date));
        }
        return $this->end_date;
    }

    public function getReason($is_decrypt = false)
    {
        $reason = $this->reason;
        if ($is_decrypt) {
            try {
                $reason = LVCCCrypto::decrypt($this->reason);
            } catch (Exception $e) {
                $reason = null;
            }
        }
        return $reason;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        if (in_array($status, [static::PENDING, static::APPROVED, static::REJECTED, static::CANCELLED])) {
            $this->status = $status;
            LeaveRequestLog::log($this->getId(), $status);
            return $this->save();
        }
        return false;
    }

    public function approve()
    {
        $this->setStatus(static::APPROVED);
    }

    public function reject()
    {
        $this->setStatus(static::REJECTED);
    }

    public function cancel()
    {
        $this->setStatus(static::CANCELLED);
    }

    public function pending()
    {
        $this->setStatus(static::PENDING);
    }

    public static function countApproved()
    {
        return static::where('status', static::APPROVED)->count();
    }

    public static function countPending()
    {
        return static::where('status', static::PENDING)->count();
    }

    public static function getByUser($user_id)
    {
        $user = User::find($user_id);
        if (is_null($user)) return null;
        $employee = $user->employee;
        return static::leftJoin('leave_types', 'leave_requests.leave_type_id', 'leave_types.id')
            ->where('leave_requests.employee_id', $employee->id)
            ->select('leave_requests.id', 'leave_requests.status', 'leave_requests.created_at', 'leave_types.leave')
            ->get();
    }

    public static function getByUserAndType($user_id, $type = null)
    {
        if (is_null($type)) {
            return static::getByUser($user_id);
        }
        $user = User::find($user_id);
        if (is_null($user)) return null;
        $employee = $user->employee;
        return static::leftJoin('leave_types', 'leave_requests.leave_type_id', 'leave_types.id')
            ->where('leave_requests.employee_id', $employee->id)
            ->where('leave_requests.status', $type)
            ->select('leave_requests.id', 'leave_requests.status', 'leave_requests.created_at', 'leave_types.leave')
            ->get();
    }

    public function isPending() {
        return $this->status === self::PENDING;
    }

    public function isApproved() {
        return $this->status === self::APPROVED;
    }

    public function isCancelled() {
        return $this->status === self::CANCELLED;
    }

    public function isRejected() {
        return $this->status === self::REJECTED;
    }

    public function getStatusColor() {
        if ($this->status == self::PENDING) return '#c4c41f';
        if ($this->status == self::APPROVED) return 'green';
        return 'red'; // cancelled or rejected
    }

    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

    /**
     * @param string Y-m-d date range (start, end) included
     */
    public static function generateReport($start_date = null, $end_date = null)
    {
        $report = [];
        $employees = Employee::all()->sortBy('last_name');
        foreach ($employees as $employee) {
            $leave_requests = LeaveRequest::where('employee_id', $employee->id)
                                        ->where(function($query) use ($start_date, $end_date) {
                                            if (!empty($start_date) && !empty($end_date)) {
                                                $query->where(function($query) use ($start_date, $end_date) {
                                                        $query->where('start_date', '>=', $start_date)
                                                            ->where('start_date', '<=', $end_date);
                                                    })
                                                    ->orWhere(function($query) use ($start_date, $end_date) {
                                                        $query->where('end_date', '>=', $start_date)
                                                            ->where('end_date', '<=', $end_date);
                                                    });
                                            }
                                        })
                                        ->orderBy('start_date', 'asc')
                                        ->orderBy('end_date', 'asc')
                                        ->orderBy('status', 'asc')
                                        ->get();
            $report[$employee->id]['leave_requests'] = $leave_requests;
            $report[$employee->id]['employee'] = $employee;
        }
        $report = ['records' => $report, 'date-range' => [$start_date, $end_date]];
        return $report;
    }

    public function getTotalDays()
    {
        return TimeUtility::getTotalDays($this->start_date, $this->end_date);
    }

    public static function getByDeparment($department_id)
    {
        $all_leave_requests = self::all();
        $department_leave_requests = [];
        foreach ($all_leave_requests as $leave_request) {
            $leave_dept_id = $leave_request->employee->department->id;
            if ($leave_dept_id === (int)$department_id) {
                array_push($department_leave_requests, $leave_request);
            }
        }
        return $department_leave_requests;
    }

    public function getStatusBadge($option = 1, $font_size = '14px') {
        if ($option == 1) {
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
        $all_pending_request = self::where('status', self::PENDING)->get();
        $dept_pending_count = 0;
        foreach ($all_pending_request as $request) {
            $leave_dept_id = $request->employee->department->id;
            if ($leave_dept_id === (int)$department_id) {
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
        $all_pending = self::where('status', self::PENDING)->get()->toArray();
        return count($all_pending);
    }
}
