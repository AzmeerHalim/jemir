<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = auth()->user()->role;

        return match ($role) {
            UserRole::Coordinator => redirect()->route('coordinator.dashboard'),
            UserRole::Lecturer => redirect()->route('lecturer.dashboard'),
            UserRole::ItcAdmin => redirect()->route('itc.dashboard'),
            UserRole::Committee => redirect()->route('committee.dashboard'),
            UserRole::StudentAffairs => redirect()->route('affairs.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable) {
            //
        }

        return redirect('/');
    }
}
