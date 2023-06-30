<?php

namespace App\Utilities;
use Log;

class CustomLogUtility
{
    public static function error($user_id, $method, $err_msg)
    {
        $err_log = '[User ID: ' . $user_id . '] ' . $method . ' >> Error: ' . $err_msg;
        Log::error($err_log);
        return response()->json([
            'success' => false,
            'error' => $err_msg,
        ], 400);
    }
}
