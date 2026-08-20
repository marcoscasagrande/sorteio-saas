<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthenticatedSessionController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'E-mail ou senha inválidos.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        // Admin passa por 2FA via código no e-mail antes de completar o login.
        // A sessão só é criada de verdade depois de validar o código.
        if ($user->isAdmin()) {
            Auth::logout();

            $codigo = (string) random_int(100000, 999999);
            $user->forceFill([
                'two_factor_code' => $codigo,
                'two_factor_expires_at' => now()->addMinutes(10),
            ])->save();

            try {
                Mail::raw(
                    "Seu código de acesso ao painel administrativo é: {$codigo}\n\nExpira em 10 minutos.",
                    fn ($m) => $m->to($user->email)->subject('Código de acesso — Admin')
                );
            } catch (\Throwable $e) {
                // SMTP ainda não configurado (comum logo após a instalação) —
                // registra no log pra não travar o acesso do admin.
                \Illuminate\Support\Facades\Log::warning("2FA: falha ao enviar e-mail, código para {$user->email}: {$codigo}");
            }

            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->boolean('remember'));

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();
        AuditLog::record('login', 'Login realizado', $user);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
