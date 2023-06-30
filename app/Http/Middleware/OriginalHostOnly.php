<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OriginalHostOnly
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
	$server_vars = $request->server->all();

        $host = '';
        if (isset($server_vars['HTTP_HOST'])) {
            $host = $server_vars['HTTP_HOST'];
        }

	// $allowed_domains = [
	// 	'localhost',
	// 	'localhost:8000',
	// 	'127.0.0.1:8000',
	// 	'bundy.laverdad.edu.ph',
	// 	'0498-175-176-8-147.ngrok-free.app'
	// ];
	// if (!in_array($host, $allowed_domains)) {
	// 	//abort(403, 'Unauthorized action');

	// 					$url = env('FUNNY_REDIRECT_URL');
	// 					if (empty($url)) {
	// 							$url = 'https://media.istockphoto.com/photos/funny-baby-picture-id154917415';
	// 					}
	// 					return redirect($url);
	// }
	return $next($request);
    }
}
