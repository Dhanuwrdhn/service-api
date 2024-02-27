<?php

namespace App\Http\Middleware\Verification;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $authorizationHeader = $request->header('Authorization');
        // return response()->json(['tes' => $authorizationHeader], Response::HTTP_OK);

        // return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }
}
