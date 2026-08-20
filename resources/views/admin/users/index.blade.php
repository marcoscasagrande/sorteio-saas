@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<h1 class="text-2xl font-display font-semibold mb-6">Usuários cadastrados</h1>

<form method="GET" class="mb-4">
    <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Buscar por nome ou e-mail"
           class="border border-ink/15 rounded-lg px-3 py-2 w-full max-w-sm focus:outline-none focus:ring-2 focus:ring-teal/40">
</form>

<div class="bg-card rounded-xl border border-ink/10 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 text-left text-ink/50 font-mono text-xs uppercase">
            <tr>
                <th class="p-3">Nome</th>
                <th class="p-3">E-mail</th>
                <th class="p-3">Sorteios</th>
                <th class="p-3">Pagamentos</th>
                <th class="p-3">Cadastrado em</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink/10">
            @foreach ($usuarios as $u)
                <tr class="hover:bg-ink/5 cursor-pointer" onclick="window.location='{{ route('admin.users.show', $u) }}'">
                    <td class="p-3">{{ $u->name }}</td>
                    <td class="p-3">{{ $u->email }}</td>
                    <td class="p-3">{{ $u->giveaways_count }}</td>
                    <td class="p-3">{{ $u->payments_count }}</td>
                    <td class="p-3 text-ink/40">{{ $u->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $usuarios->links() }}</div>
@endsection
