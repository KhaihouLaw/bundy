<?php

namespace App\Http\Controllers;

use App\Models\AdvancePunchClock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function index()
    {
        return view('user.advance.index');
    }

    public function accessPunchClockInstance(Request $request)
    {
        $instance = AdvancePunchClock::where('access_code', $request->access_code)->first();
        if (!empty($instance)) {
            $schedToday = $instance->schedules[strtolower(date('l'))] ?? null;
            if (!empty($schedToday)) {
                $start_time = $schedToday['start_time'];
                $end_time = $schedToday['end_time'];
                
                $passed_start_rule = strtotime(date('Y-m-d H:i')) >= strtotime($start_time);
                $passed_end_rule = strtotime(date('Y-m-d H:i')) <= strtotime($end_time);
                $is_scheduled_now = $passed_start_rule && $passed_end_rule;
                // dd('$is_scheduled_now', $is_scheduled_now); @remind TODO
            }
            // dd($schedToday, date('l')); @remind TODO
        }
        return redirect()->back()->with(['error' => "Instance doesn't exist"]);  
    }

    public function authorizePunchClockInstance()
    {
        return view('user.advance.punch-clock.authorize');
    }

    public function registerFace()
    {
        return view('user.advance.face-recognition.register');
    }

    public function clockFace()
    {
        return view('user.advance.face-recognition.clock');
    }
}
