<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Tipo de Conta</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cadastro/style.css') }}">
</head>

<body>
    <header class="header">
        <img src="{{ asset('assets/images/logos/intea/logo.png') }}" alt="Logo" class="logo">
    </header>

    <main class="container">
        <section class="form">
            <h1>Escolha o tipo de conta</h1>
            <p>Selecione uma das opções abaixo para continuar com o cadastro:</p>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.autista') }}">Autista</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.comunidade') }}">Comunidade</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.responsavel') }}">Responsavel</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.profissionalsaude') }}">Profissional da Saúde</a></p>
            </button>

            <div class="registro">
                <p>Já possui uma conta? <a href="{{ route('welcome') }}">Entre aqui</a></p>
            </div>
        </section>

    </main>
</body>

</html>

{{--  <x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

         Name 
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

         Email Address 
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

         Password 
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

         Confirm Password 
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}