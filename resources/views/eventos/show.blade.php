@extends('layouts.app')

@section('title', $evento->titulo . ' — FalaQ')

@section('content')
<div class="row">
    <!-- Formulário de envio de Pergunta -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm p-3">
            <h4 class="fw-bold mb-3">💬 Faça sua Pergunta</h4>
            <form action="{{ route('eventos.perguntas.store', $evento->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="texto" class="form-label text-secondary">Texto da Pergunta</label>

                    <textarea name="texto" id="texto" rows="4" 
                            class="form-control bg-dark text-white border-secondary @error('texto') is-invalid @enderror"
                            placeholder="Digite sua dúvida ou comentário para o palestrante..."></textarea>

                    @error('texto')
                        <div class="invalid-feedback fw-bold">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Enviar Pergunta</button>
            </form>
        </div>
    </div>

    <!-- Lista de Perguntas (TICKET #002 e #003) -->
    <div class="col-md-7">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">📋 Perguntas do Evento</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small">Total no Banco: {{ $evento->perguntas->count() }}</span>
                @can('moderate', $evento)
                    <a href="{{ route('eventos.moderacoes.index', $evento) }}" class="btn btn-sm btn-outline-warning">Fila de moderação</a>
                @endcan
            </div>
        </div>

        @forelse($perguntas as $pergunta)
            <div class="card mb-3 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <p class="fs-5 mb-2 text-white">{{ $pergunta->texto }}</p>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-secondary">{{ $pergunta->votos_count }} votos</span>

                        @auth
                            @php($usuarioJaVotou = $pergunta->votos->contains(auth()->id()))
                            <form action="{{ route('perguntas.votar', $pergunta) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $usuarioJaVotou ? 'btn-warning' : 'btn-outline-light' }}">
                                    {{ $usuarioJaVotou ? 'Remover voto' : '▲ Upvote' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Entre para votar</a>
                        @endauth
                    </div>
                    @auth
                        <details class="mb-3">
                            <summary class="text-secondary small">Reportar conteúdo impróprio</summary>
                            <form action="{{ route('perguntas.moderar', $pergunta) }}" method="POST" class="mt-2">
                                @csrf
                                <textarea name="motivo" rows="2" maxlength="255" class="form-control form-control-sm" placeholder="Explique o motivo da denúncia (mínimo de 10 caracteres)."></textarea>
                                <button type="submit" class="btn btn-sm btn-outline-warning mt-2">Enviar denúncia</button>
                            </form>
                        </details>
                    @endauth
                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>👤 {{ $pergunta->user->name ?? 'Anônimo' }}</span>
                        <span>Status: <span class="badge bg-success">{{ $pergunta->status }}</span></span>
                        <span>{{ $pergunta->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-dark text-center p-4">
                Nenhuma pergunta enviada ainda. Seja o primeiro!
            </div>
        @endforelse

        @if(method_exists($perguntas, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $perguntas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
