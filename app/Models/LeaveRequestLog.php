<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestLog extends Model
{
    use HasFactory;

    public static function log($request_id, $status)
    {
        $log = new static;
        $log->leave_request_id = $request_id;
        $log->status = $status;
        return $log->save();
    }
}
