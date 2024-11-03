<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use ProcessApiRequestLog;
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

        // Queue-a ataraq işlənməyə göndəririk
        Queue::push(new ProcessApiRequestLog($ipAddress, $date));

        return $next($request);
    }
}
