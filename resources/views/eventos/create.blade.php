@extends('layouts.app')

@section('title', 'Criar Evento — FalaQ')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md text-gray-900">
    <h1 class="text-2xl font-bold mb-6">Criar novo evento</h1>

    <form action="{{ route('eventos.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}"
                class="w-full border border-gray-300 p-2 rounded-md @error('titulo') border-red-500 @enderror">
            @error('titulo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea name="descricao" id="descricao" rows="5"
                class="w-full border border-gray-300 p-2 rounded-md @error('descricao') border-red-500 @enderror">{{ old('descricao') }}</textarea>
            @error('descricao')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="data_evento" class="block text-sm font-medium text-gray-700 mb-1">Data do evento</label>
            <input type="datetime-local" name="data_evento" id="data_evento" value="{{ old('data_evento') }}"
                class="w-full border border-gray-300 p-2 rounded-md @error('data_evento') border-red-500 @enderror">
            @error('data_evento')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Criar evento
        </button>
    </form>
</div>
@endsection
