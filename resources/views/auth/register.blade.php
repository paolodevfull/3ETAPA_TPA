@extends('layouts.app')

@section('title', 'Criar conta — FalaQ')

@section('content')
<form action="{{ route('register.store') }}" method="POST" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md text-gray-900 space-y-4">
    @csrf
    <h1 class="text-2xl font-bold">Criar conta</h1>

    @foreach ([['name', 'Nome', 'text'], ['email', 'E-mail', 'email']] as [$field, $label, $type])
        <div>
            <label for="{{ $field }}" class="block mb-1">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $field }}" id="{{ $field }}" value="{{ old($field) }}"
                class="w-full border border-gray-300 p-2 rounded-md @error($field) border-red-500 @enderror">
            @error($field)<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
    @endforeach

    <div>
        <label for="password" class="block mb-1">Senha</label>
        <input type="password" name="password" id="password"
            class="w-full border border-gray-300 p-2 rounded-md @error('password') border-red-500 @enderror">
        @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="block mb-1">Confirmar senha</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 p-2 rounded-md">
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Criar conta</button>
</form>
@endsection
