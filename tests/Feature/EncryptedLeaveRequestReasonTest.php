<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\LeaveRequest;
use App\Utilities\LVCCCrypto;

class EncryptedLeaveRequestReasonTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLeaveRequest()
    {
	$original_data = bin2hex(random_bytes(30));
	$leaveRequest = LeaveRequest::create([
		'employee_id' => 1,
		'leave_type_id' => 1,
		'reason' => LVCCCrypto::encrypt($original_data),
		'assigned_reviewer_id' => 1,
		'reviewed_by' => 1
        ]);
	$decrypted_data = $leaveRequest->getReason(true);

	$this->assertEquals($original_data, $decrypted_data);
    }
}
