<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci a senha</title>
<link rel="stylesheet" href="{{ asset('assets/css/auth/esqueceu-senha.css') }}"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="http://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ url('assets/css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
</head>
<body>
     <div class="login-container">
        <a class="voltar" href="{{ route('login') }}"><img src="{{ asset('assets/images/logos/symbols/back-button.png') }}"></a>
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e enviaremos um link para redefinição de senha que permitirá que você escolha uma nova.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Link de redefinição de senha de e-mail') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

</body>
</html>

        <div class="login-content">
            <img class="logo-esqueceu" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
          
              