<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('student_authenticated') || !session('student_authenticated')) {
            return redirect()->route('login')->withErrors(['message' => 'Please log in to access this page.']);
        }

        $response = $next($request);

        // Prevent caching to avoid back button access after logout
        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
