<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * HTTP methods that modify data and should be blocked in demo mode
     */
    protected array $blockedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Routes that are allowed even in demo mode (e.g., logout)
     */
    protected array $allowedRoutes = [
        'logout',
        'admin.login',
        'unified.login',
        'student.logout',
    ];

    /**
     * Handle an incoming request.
     * Block write operations for demo users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is in demo mode
        $isDemo = session('is_demo', false) ||
            strtolower(session('admin_username', '')) === 'demo@gmail.com';

        // If not demo mode, proceed normally
        if (!$isDemo) {
            return $next($request);
        }

        // Check if this route is allowed in demo mode
        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->allowedRoutes)) {
            return $next($request);
        }

        // Block write operations (POST, PUT, PATCH, DELETE) in demo mode
        if (in_array($request->method(), $this->blockedMethods)) {
            // For AJAX requests, return JSON error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demo Mode: This action is disabled for demonstration purposes.',
                    'demo_mode' => true
                ], 403);
            }

            // For regular requests, redirect back with error
            return redirect()->back()->with('demo_error', 'Demo Mode: This action is disabled for demonstration purposes.');
        }

        return $next($request);
    }
}
