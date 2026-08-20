<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    // Painel do usuário logado (não-admin)
    public function dashboard()
    {
        $user = auth()->user();

        $giveaways = $user->giveaways()->latest()->paginate(10);
        $temInstagramConectado = $user->instagramTokens()->exists();

        return view('dashboard', compact('giveaways', 'temInstagramConectado'));
    }
}
