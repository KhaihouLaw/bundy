<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveReqListController extends Controller
{
    public function showAll()
    {
        $leaveRequestLists = LeaveRequest::getByUser(Auth::id());

        return view('leave-requests.all-leave-requests', [
            'leaveRequestLists' => $leaveRequestLists
        ]);
    }

    public function showApproved()
    {
        $leaveRequestLists = LeaveRequest::getByUserAndType(Auth::id(), LeaveRequest::APPROVED);

        return view('leave-requests.approved', [
            'leaveRequestLists' => $leaveRequestLists
        ]);
    }

    public function showPending()
    {
        $leaveRequestLists = LeaveRequest::getByUserAndType(Auth::id(), LeaveRequest::PENDING);

        return view('leave-requests.pending', [
            'leaveRequestLists' => $leaveRequestLists
        ]);
    }

    public function showCancelled()
    {
        $leaveRequestLists = LeaveRequest::getByUserAndType(Auth::id(), LeaveRequest::CANCELLED);

        return view('leave-requests.cancelled', [
            'leaveRequestLists' => $leaveRequestLists
        ]);
    }

    public function showRejected()
    {
        $leaveRequestLists = LeaveRequest::getByUserAndType(Auth::id(), LeaveRequest::REJECTED);

        return view('leave-requests.rejected', [
            'leaveRequestLists' => $leaveRequestLists
        ]);
    }

    public function modal_view($id)
    {
        return LeaveRequest::findOrFail($id);
    }
}
