<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventoFormRequest;
use App\Http\Requests\StorePerguntaRequest;
use App\Models\Evento;
use App\Models\Pergunta;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::all();
        return view('eventos.index', compact('eventos'));
    }

    /**
     * TICKET #002 & #004 (PERFORMANCE & CARREGAMENTO ANSIOSO)
     */
    public function show(mixed $id)
    {
        /** @var Evento $evento */
        $evento = Evento::findOrFail($id);

        $perguntas = Pergunta::with('user')
                    ->where('evento_id', $evento->getKey())
                    ->where('is_public', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('eventos.show', compact('evento', 'perguntas'));
    }

    /**
     * TICKET #001 & #003
     */
    public function storePergunta(StorePerguntaRequest $request, mixed $id)
    {
        /** @var Evento $evento */
        $evento = Evento::findOrFail($id);

        Pergunta::create([
            'evento_id' => $evento->getKey(),
            'user_id'   => auth()->id(),
            'texto'     => $request->input('texto'),
            'status'    => 'pendente',
        ]);

        return redirect()->route('eventos.show', $evento->getKey())
            ->with('sucesso', 'Sua pergunta foi enviada com sucesso!');
    }

    public function create()
    {
        return view('eventos.create');
    }

    public function store(EventoFormRequest $request)
    {
        $evento = $request->user()->eventos()->create($request->validated());

        return redirect()->route('eventos.show', $evento->getKey())
            ->with('sucesso', 'Evento criado com sucesso!');
    }
}
