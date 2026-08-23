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

        // แปลงรายการ Roles ที่ส่งเข้ามาใน Middleware ให้เป็นตัวพิมพ์เล็กทั้งหมด
        $allowedRoles = array_map('strtolower', $roles);

        // ถ้าผู้ใช้เป็น patient แต่เข้าถึง route ที่ไม่เปิดสิทธิ์ให้ patient ให้ Redirect ไปที่ queue.history
        if ($user->isPatient() && ! in_array('patient', $allowedRoles, true)) {
            return redirect()->route('queue.history');
        }

        // เช็คสิทธิ์ด้วย method hasRole() ใน User Model (ซึ่งรองรับการแปลงตัวพิมพ์เล็ก/ใหญ่แล้ว)
        if (! $user->hasRole($allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}