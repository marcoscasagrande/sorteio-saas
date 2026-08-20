<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planos = Plan::orderBy('sort_order')->orderBy('price')->get();

        return view('admin.plans.index', compact('planos'));
    }

    public function create()
    {
        return view('admin.plans.form', ['plano' => new Plan()]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validarDados($request));

        return redirect()->route('admin.plans.index')->with('sucesso', 'Plano criado com sucesso.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.form', ['plano' => $plan]);
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validarDados($request));

        return redirect()->route('admin.plans.index')->with('sucesso', 'Plano atualizado com sucesso.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return back()->with('sucesso', 'Plano removido.');
    }

    private function validarDados(Request $request): array
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'in:unico,mensal,anual'],
            'giveaways_per_period' => ['nullable', 'integer', 'min:1'],
        ]);

        $dados['is_featured'] = $request->boolean('is_featured');
        $dados['active'] = $request->boolean('active');
        $dados['sort_order'] = $request->integer('sort_order', 0);

        return $dados;
    }
}
