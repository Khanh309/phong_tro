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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isTenant()) {
            if ($request->isMethod('GET')) {
                return redirect()->route('portal.index')
                    ->with('error', 'Bạn đang đăng nhập bằng tài khoản Khách Thuê. Không có quyền truy cập trang quản lý.');
            }
            abort(403, 'Tài khoản khách thuê không có quyền thực hiện thao tác quản trị này.');
        }

        return $next($request);
    }
}
