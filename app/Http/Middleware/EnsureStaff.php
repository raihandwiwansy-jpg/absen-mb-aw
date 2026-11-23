<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If the user only has member role without any administrative/officer role
        if ($user->hasRole('member') && ! $user->hasAnyRole(['super_admin', 'administrator', 'attendance_officer', 'coach'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur ini khusus untuk pengurus dan petugas marching band.',
                ], 403);
            }

            return redirect()->route('portal.dashboard')
                ->with('error', 'Akses dibatasi. Halaman tersebut khusus untuk pengurus dan petugas marching band.');
        }

        return $next($request);
    }
}
