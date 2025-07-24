<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informações do Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Atualize as informações do seu perfil.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nome" :value="__('Nome')" />
            <x-text-input id="nome" nome="nome" type="text" class="mt-1 block w-full" :value="old('nome', $user->nome)" required autofocus autocomplete="nome" />
            <x-input-error class="mt-2" :messages="$errors->get('nome')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Clique aqui para enviar o e-mail de verificação.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <div>
            <x-input-label for="apelido" value="Apelido" />
            <x-text-input id="apelido" name="apelido" type="text" class="mt-1 block w-full" :value="old('apelido', $user->apelido)" />
            <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
        </div>

        <div>
            <x-input-label for="user" value="Nome user" />
            <x-text-input id="user" name="user" type="text" class="mt-1 block w-full" :value="old('user', $user->user)" />
            <x-input-error class="mt-2" :messages="$errors->get('user')" />
        </div>

        <div>
            <x-input-label for="tipo_usuario" value="Tipo conta" />
            @if($user->tipo_usuario===1)
                <h2>Admin</h2>
            @elseif($user->tipo_usuario===2)
                <h2>Admin</h2>
            @elseif($user->tipo_usuario===3)
                <h2>Comunidade</h2>
            @elseif($user->tipo_usuario===4)
                <h2>Profissional de Saúde</h2>
            @elseif($user->tipo_usuario===5)
                <h2>Responsável</h2>
            @endif
        </div>

        <div>
            <x-input-label for="cpf" value="CPF" />
            <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full" :value="old('cpf', $user->cpf)" />
            <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
        </div>

        <div>
            <x-input-label for="genero" value="Gênero" />
            <x-text-input id="genero" name="genero" type="text" class="mt-1 block w-full" :value="old('genero', $user->genero)" />
            <x-input-error class="mt-2" :messages="$errors->get('genero')" />
        </div>

        <div>
            <x-input-label for="data_nascimento" value="Data de Nascimento" />
            <x-text-input id="data_nascimento" name="data_nascimento" type="date" class="mt-1 block w-full" :value="old('data_nascimento', $user->data_nascimento)" />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <div>
            <x-input-label for="logradouro" value="Logradouro" />
            <x-text-input id="logradouro" name="logradouro" type="text" class="mt-1 block w-full" :value="old('logradouro', $user->logradouro)" />
            <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
        </div>

        <div>
            <x-input-label for="endereco" value="Endereço" />
            <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full" :value="old('endereco', $user->endereco)" />
            <x-input-error class="mt-2" :messages="$errors->get('endereco')" />
        </div>

        <div>
            <x-input-label for="rua" value="Rua" />
            <x-text-input id="rua" name="rua" type="text" class="mt-1 block w-full" :value="old('rua', $user->rua)" />
            <x-input-error class="mt-2" :messages="$errors->get('rua')" />
        </div>

        <div>
            <x-input-label for="bairro" value="Bairro" />
            <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full" :value="old('bairro', $user->bairro)" />
            <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
        </div>

        <div>
            <x-input-label for="numero" value="Número" />
            <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero', $user->numero)" />
            <x-input-error class="mt-2" :messages="$errors->get('numero')" />
        </div>

        <div>
            <x-input-label for="cidade" value="Cidade" />
            <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full" :value="old('cidade', $user->cidade)" />
            <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
        </div>

        <div>
            <x-input-label for="estado" value="Estado" />
            <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $user->estado)" />
            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
        </div>

        <div>
            <x-input-label for="complemento" value="Complemento" />
            <x-text-input id="complemento" name="complemento" type="text" class="mt-1 block w-full" :value="old('complemento', $user->complemento)" />
            <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>