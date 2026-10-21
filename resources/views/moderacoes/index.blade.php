@extends('layouts.app')

@section('title', 'Fila de moderação — FalaQ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Fila de moderação</h1>
        <p class="text-secondary mb-0">{{ $evento->titulo }}</p>
    </div>
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-outline-light">Voltar ao evento</a>
</div>

@forelse($moderacoes as $moderacao)
    <article class="card mb-3 shadow-sm">
        <div class="card-body">
            <p class="mb-1 text-white"><strong>Pergunta:</strong> {{ $moderacao->pergunta->texto }}</p>
            <p class="mb-3 text-secondary"><strong>Motivo:</strong> {{ $moderacao->motivo }}</p>
            <div class="d-flex justify-content-between align-items-center gap-3">
                <span class="badge bg-secondary">{{ ucfirst($moderacao->status) }}</span>
                @if($moderacao->status === 'analisando')
                    <form action="{{ route('moderacoes.update-status', $moderacao) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <button name="status" value="aprovada" type="submit" class="btn btn-sm btn-success">Aprovar pergunta</button>
                        <button name="status" value="rejeitada" type="submit" class="btn btn-sm btn-danger">Rejeitar pergunta</button>
                    </form>
                @endif
            </div>
        </div>
    </article>
@empty
    <div class="alert alert-dark">Nenhuma denúncia para este evento.</div>
@endforelse

{{ $moderacoes->links() }}
@endsection
