<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::query()
            ->withCount(['giveaways', 'payments'])
            ->when($request->busca, fn ($q) => $q->where('name', 'like', "%{$request->busca}%")
                ->orWhere('email', 'like', "%{$request->busca}%"))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('usuarios'));
    }

    public function show(User $user)
    {
        $user->load(['giveaways', 'payments', 'instagramTokens']);

        return view('admin.users.show', compact('user'));
    }
}
