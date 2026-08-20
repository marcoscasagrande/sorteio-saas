@extends('layouts.app')

@section('title', 'Logs de auditoria')

@section('content')
<h1 class="text-2xl font-display font-semibold mb-6">Logs de auditoria</h1>

<form method="GET" class="mb-4">
    <input type="text" name="acao" value="{{ request('acao') }}" placeholder="Filtrar por ação (ex: login, sorteio.sortear)"
           class="border border-ink/15 rounded-lg px-3 py-2 w-full max-w-sm">
</form>

<div class="bg-card rounded-xl border border-ink/10 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 text-left text-ink/50 font-mono text-xs uppercase">
            <tr>
                <th class="p-3">Quando</th>
                <th class="p-3">Usuário</th>
                <th class="p-3">Ação</th>
                <th class="p-3">Detalhe</th>
                <th class="p-3">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink/10">
            @forelse ($logs as $log)
                <tr>
                    <td class="p-3 text-ink/40 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-3">{{ $log->user->name ?? '—' }}</td>
                    <td class="p-3"><span class="badge-teal">{{ $log->action }}</span></td>
                    <td class="p-3 text-ink/60">{{ $log->description }}</td>
                    <td class="p-3 text-ink/40 font-mono text-xs">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-ink/40">Nenhum log registrado ainda.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
