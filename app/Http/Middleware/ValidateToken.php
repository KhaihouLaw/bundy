<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Exception;
use Log;

class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $bearerToken = $request->bearerToken() ?? $request->get('token');
            if (!$bearerToken) {
                throw new Exception('No login token, please login');
            }

            $user = User::where('login_token', $bearerToken)->first();
            if (!$user) {
                throw new Exception('Login token does not exist in database, please contact the HR for assistance');
            } else if (strtotime($user->token_created_at) < strtotime('-30 days')) {
                throw new Exception('Expired login token, please login again');
            }

            $isMobileApp = $request->header('LVCC-Bundy-App') ?? false;
            $request->attributes->add(['isMobileApp' => $isMobileApp]);

            $request->attributes->add(['currentUser' => $user]);
            Auth::login($user);

            return $next($request);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => true,
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
