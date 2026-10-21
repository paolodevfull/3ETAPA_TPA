<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModeracaoRequest;
use App\Models\Evento;
use App\Models\Moderacao;
use App\Models\Pergunta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeracaoController extends Controller
{
    public function store(StoreModeracaoRequest $request, Pergunta $pergunta): RedirectResponse
    {
        $pergunta->moderacoes()->create($request->validated());

        return back()->with('sucesso', 'Denúncia enviada para análise.');
    }

    public function index(Evento $evento): View
    {
        $this->authorize('moderate', $evento);

        $moderacoes = Moderacao::with('pergunta')
            ->whereHas('pergunta', fn ($query) => $query->where('evento_id', $evento->getKey()))
            ->latest()
            ->paginate(15);

        return view('moderacoes.index', compact('evento', 'moderacoes'));
    }

    public function updateStatus(Request $request, Moderacao $moderacao): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:aprovada,rejeitada'],
        ]);

        $moderacao->loadMissing('pergunta.evento');
        $this->authorize('moderate', $moderacao->pergunta->evento);

        $moderacao->update($validated);
        $moderacao->pergunta->update([
            'is_public' => $validated['status'] === 'aprovada',
        ]);

        return back()->with('sucesso', 'Status da denúncia atualizado.');
    }
}
