<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isTenant()) {
                return redirect()->route('portal.index');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isTenant()) {
                if ($user->tenant_id) {
                    session(['tenant_id' => $user->tenant_id]);
                }
                return redirect()->route('portal.index')
                    ->with('success', "Xin chào {$user->name}! Bạn đã đăng nhập vào Cổng Khách Thuê thành công.");
            }

            $roleLabel = $user->isAdmin() ? 'Chủ Nhà Trọ (Toàn quyền)' : 'Quản Lý Cơ Sở';

            return redirect()->intended(route('dashboard'))
                ->with('success', "Xin chào {$user->name}! Bạn đã đăng nhập với vai trò {$roleLabel}.");
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $isTenant = Auth::check() && Auth::user()->isTenant();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất khỏi hệ thống thành công.');
    }
}
