@extends('layouts.app')

@section('title', $user->name)

@section('content')
<a href="{{ route('admin.users.index') }}" class="text-sm text-teal font-medium">&larr; Voltar</a>

<h1 class="text-2xl font-display font-semibold my-4">{{ $user->name }}</h1>
<p class="text-ink/50 mb-6">{{ $user->email }} · cadastrado em {{ $user->created_at->format('d/m/Y H:i') }}</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-card rounded-xl border border-ink/10">
        <div class="p-4 border-b border-ink/10 font-display font-semibold">Sorteios</div>
        <div class="divide-y divide-ink/10">
            @forelse ($user->giveaways as $g)
                <div class="p-3 text-sm flex justify-between">
                    <span>{{ $g->instagram_post_url }}</span>
                    <span class="text-ink/40 font-mono text-xs">{{ $g->status }}</span>
                </div>
            @empty
                <p class="p-3 text-sm text-ink/40">Nenhum sorteio ainda.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-card rounded-xl border border-ink/10">
        <div class="p-4 border-b border-ink/10 font-display font-semibold">Pagamentos</div>
        <div class="divide-y divide-ink/10">
            @forelse ($user->payments as $p)
                <div class="p-3 text-sm flex justify-between">
                    <span>R$ {{ number_format($p->amount, 2, ',', '.') }}</span>
                    <span class="text-ink/40 font-mono text-xs">{{ $p->status }}</span>
                </div>
            @empty
                <p class="p-3 text-sm text-ink/40">Nenhum pagamento ainda.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
