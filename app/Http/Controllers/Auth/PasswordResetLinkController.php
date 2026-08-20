<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            // SMTP ainda não configurado ou indisponível — não trava o fluxo,
            // só registra no log pra investigar depois.
            Log::warning("Reset de senha: falha ao enviar e-mail para {$request->email}: {$e->getMessage()}");
        }

        AuditLog::record('senha.link_solicitado', "Link de redefinição solicitado para {$request->email}");

        // Mensagem sempre igual, exista ou não o e-mail cadastrado — evita
        // que alguém descubra quais e-mails têm conta só tentando aqui.
        return back()->with('sucesso', 'Se esse e-mail estiver cadastrado, enviamos um link de redefinição.');
    }
}
