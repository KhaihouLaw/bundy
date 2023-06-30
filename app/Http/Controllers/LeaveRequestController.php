<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\User;
use App\Utilities\CustomLogUtility;
use App\Utilities\LVCCCrypto;
use App\Utilities\EmailUtility;
use Auth;
use Exception;
use Log;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all()->toArray();
        $employee = Auth::user()->employee;
        return view('user.leave-request', compact('leaveTypes', 'employee'));
    }

    public function getLeaveRequest(Request $request, $id)
    {
        $isMobileApp = $request->get('isMobileApp');
        $leaveRequest = LeaveRequest::find($id);
        $leaveRequest->load(['leaveType', 'assignedReviewer']);
        $leaveRequest->reason = $leaveRequest->getReason(true);
        if ($isMobileApp) {
            return response()->json(['data' => $leaveRequest]);    
        }
        return response()->json($leaveRequest);
    }

    public function getLeaveRequests(Request $request)
    {
        $leaveTypeId = $request->get('leave_type_id');
        $lastDate = $request->get('last_date');
        $status = $request->get('status');

        $requests = LeaveRequest::where('employee_id', Auth::user()->employee_id)
                                ->when($leaveTypeId, function($query) use ($leaveTypeId) {
                                    $query->where('leave_type_id', $leaveTypeId);
                                })
                                ->when($lastDate, function($query) use ($lastDate) {
                                    $query->where('start_date', '<', $lastDate);
                                })
                                ->when($status, function($query) use ($status) {
                                    $query->where('status', $status);
                                })
                                ->with(['leaveType', 'assignedReviewer'])
                                ->orderBy('start_date', 'desc')
                                ->limit(10)
                                ->get();

        return response()->json(['data' => $requests]);
    }

    public function getLeaveApprovers(Request $request)
    {
        $approvers = User::role('leave approver')->get(); 
        $approvers->load('employee');
        return response()->json(['data' => $approvers]);
    }

    public function getLeaveTypes(Request $request)
    {
        $isMobileApp = $request->get('isMobileApp');
        $types = LeaveType::all();
        
        if ($isMobileApp) {
            return response()->json(['data' => $types]);
        }

        return response()->json($types);
    }

    public function submitLeaveRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            } 
            $reason = $request->reason;
            $is_proceed_save = true;
            $leaveType = LeaveType::find($request->leave_type_id);
            if (is_null($leaveType)) {
                $is_proceed_save = false;
            }
            $reviewer = Employee::find($request->assigned_reviewer_id);
            if (is_null($reviewer)) {
                $is_proceed_save = false;
            }
            if ($is_proceed_save) {
                // Apply encryption
                $reason = LVCCCrypto::encrypt($reason);
                $leaveRequest = LeaveRequest::create([
                    'employee_id' => Auth::user()->employee_id,
                    'leave_type_id' => $leaveType->id,
                    'reason' => $reason,
                    'assigned_reviewer_id' => $reviewer->getId(),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
                // Leave Request is in 'pending' status by default
                $leaveRequest->setStatus(LeaveRequest::PENDING);
                if (!is_null($leaveRequest)) {
                    // send email notif
                    EmailUtility::sendLeaveRequestStatusNotif([
                        'requestor' => [
                            'subject' => 'Your Leave Request is Pending',
                        ],
                        'reviewer' => [
                            'subject' => 'New Leave Request ',
                        ],
                    ], $leaveRequest);
                    return response()->json(['success' => true]);
                }
            }
            throw new Exception('Something went wrong');
        } catch (Exception $e) {
            $log_err = 'User ID: ' . Auth::id() . '; Error: ' . $e->getMessage();
            Log::error($log_err);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function approveLeaveRequest(Request $request)
    {
        try {
            $leaveRequest = LeaveRequest::find($request->leave_request_id);
            if (!empty($leaveRequest) && $leaveRequest->isPending()) {
                $leaveRequest->approve();
                EmailUtility::sendLeaveRequestStatusNotif([
                    'requestor' => [
                        'subject' => 'Your Leave Request is Approved',
                        'from-label' => 'Approved by:',
                    ],
                    'reviewer' => [
                        'subject' => 'You Approved a Leave Request',
                    ]
                ], $leaveRequest);
                return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request!');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function rejectLeaveRequest(Request $request)
    {
        try {
            $leaveRequest = LeaveRequest::find($request->leave_request_id);
            if (!empty($leaveRequest) && $leaveRequest->isPending()) {
                $leaveRequest->reject();
                EmailUtility::sendLeaveRequestStatusNotif([
                    'requestor' => [
                        'subject' => 'Your Leave Request is Rejected',
                        'from-label' => 'Rejected by:',
                    ],
                    'reviewer' => [
                        'subject' => 'You Rejected a Leave Request',
                    ]
                ], $leaveRequest);
                return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request!');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function cancelLeaveRequest(Request $request)
    {
        try {
            $leaveRequest = LeaveRequest::find($request->leave_request_id);
            if (!empty($leaveRequest) && $leaveRequest->isPending()) {
                $leaveRequest->cancel();
                EmailUtility::sendLeaveRequestStatusNotif([
                    'requestor' => [
                        'subject' => 'You Cancelled Your Leave Request',
                    ],
                    'reviewer' => [
                        'subject' => 'A Leave Request Request is Cancelled',
                        'email' => env('HR_EMAIL'),
                    ]
                ], $leaveRequest);
                return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request!');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function showAll()
    {
        $leaveRequestLists = LeaveRequest::getByUser(Auth::id());

        return view('leave-requests.all-leave-requests', compact('leaveRequestLists'));
    }

    public function showAllById($id){
        $leaveRequestLists = LeaveRequest::where("id", $id)->get();

        return view(compact('leaveRequestLists'));
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
        $leaveRequestLists = LeaveRequest::where('status', 'pending')->get();

        return response()->json($leaveRequestLists);
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

    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

    /**
     * @return view
     */
    public function evaluate($id, $option = 1)
    {
        try {
            $leave_request_model = LeaveRequest::find($id);
            if (empty($leave_request_model)) throw new Exception('Bad Request!');
            if ($option == 1) {
                return view(
                    'user.advance.supervisor.components.leave-request-reason', 
                    compact('leave_request_model')
                );
            } elseif ($option == 2) {
                return view(
                    'admin.components.leave-request-reason', 
                    compact('leave_request_model')
                );
            } else throw new Exception('Bad Request!');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }
}
