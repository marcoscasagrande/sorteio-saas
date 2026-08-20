<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = $request->session()->get('2fa_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        $valido = $user->two_factor_code
            && $user->two_factor_code === trim($request->code)
            && $user->two_factor_expires_at
            && $user->two_factor_expires_at->isFuture();

        if (! $valido) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        $user->forceFill(['two_factor_code' => null, 'two_factor_expires_at' => null])->save();

        Auth::login($user, $request->session()->pull('2fa_remember', false));
        $request->session()->regenerate();
        $request->session()->forget('2fa_user_id');

        AuditLog::record('login', 'Login realizado (2FA confirmado)', $user);

        return redirect()->intended(route('admin.dashboard'));
    }
}
