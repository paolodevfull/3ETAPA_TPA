@extends('layouts.app')

@section('title', 'Login — FalaQ')

@section('content')
<form action="{{ route('login.store') }}" method="POST" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md text-gray-900 space-y-4">
    @csrf
    <h1 class="text-2xl font-bold">Entrar</h1>

    <div>
        <label for="email" class="block mb-1">E-mail</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}"
            class="w-full border border-gray-300 p-2 rounded-md @error('email') border-red-500 @enderror">
        @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="block mb-1">Senha</label>
        <input type="password" name="password" id="password"
            class="w-full border border-gray-300 p-2 rounded-md @error('password') border-red-500 @enderror">
        @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Entrar</button>
</form>
@endsection
