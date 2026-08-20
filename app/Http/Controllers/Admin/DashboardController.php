<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_usuarios' => User::where('role', 'user')->count(),
            'cadastros_hoje' => User::whereDate('created_at', today())->count(),
            'sorteios_total' => Giveaway::count(),
            'sorteios_aguardando_pagamento' => Giveaway::where('status', 'pending_payment')->count(),
            'faturamento_total' => Payment::where('status', 'approved')->sum('amount'),
            'faturamento_mes' => Payment::where('status', 'approved')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
        ];

        $ultimosUsuarios = User::latest()->limit(10)->get();
        $ultimosPagamentos = Payment::with(['user', 'giveaway'])->latest()->limit(10)->get();

        return view('admin.dashboard', compact('metrics', 'ultimosUsuarios', 'ultimosPagamentos'));
    }
}
