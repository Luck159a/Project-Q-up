<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // ถ้าผู้ใช้เป็น patient แต่เข้าถึง route ที่ต้องใช้สิทธิ์ admin/staff ให้ Redirect ไปที่ queue.history
        if ($user->isPatient() && ! in_array('patient', $roles, true)) {
            return redirect()->route('queue.history');
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}