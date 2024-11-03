<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessApiRequestLog;
use Closure;
use Illuminate\Http\Request;


use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Queue;


class LogApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $ipAddress = $request->ip();
        $date = now()->toDateString();
        $userAgent = $request->header('User-Agent');

        Queue::push(new ProcessApiRequestLog($ipAddress, $date, $userAgent));

        return $next($request);
    }
}
