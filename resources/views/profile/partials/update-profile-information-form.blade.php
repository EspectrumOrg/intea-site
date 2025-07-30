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

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input id="nome" nome="nome" type="text" class="form-control" value="{{ $user->nome ?? old('nome') }}" required autofocus autocomplete="nome" />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ $user->email ?? old('email') }}" required autocomplete="username" />


            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Seu endereço de email não é verificado.') }}

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

        <div class="mb-3">
            <label for="nome" class="form-label">Apelido</label>
            <input id="apelido" name="apelido" type="text" class="form-control" value="{{ $user->apelido ?? old('apelido')}}" required autocomplete="apelido" />
            <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome User</label>
            <input id="user" name="user" type="text" class="form-control" value="{{ $user->user ?? old('user')}}" required autocomplete="user" />
            <x-input-error class="mt-2" :messages="$errors->get('user')" />
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Tipo Conta</label>
            <x-input-label for="tipo_usuario" />
            @if($user->tipo_usuario===1)
            <h3>Admin</h3>
            @elseif($user->tipo_usuario===2)
            <h3>Admin</h3>
            @elseif($user->tipo_usuario===3)
            <h3>Comunidade</h3>
            @elseif($user->tipo_usuario===4)
            <h3>Profissional de Saúde</h3>
            @elseif($user->tipo_usuario===5)
            <h3>Responsável</h3>
            @endif
        </div>

        {{-- adicionar campos específicos para cada tipo de conta (tirando admin e comunidade)--}}

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input id="cpf" name="cpf" type="text" class="form-control" value="{{ $user->cpf ?? old('cpf')}}" required autocomplete="cpf" />
            <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
        </div>

        {{--
        <div class="mb-3">
            <label for="genero" class="form-label">Gênero</label>
            <input id="genero" name="genero" type="text" class="form-control" value="{{ $user->genero ?? old('genero')}}" required autocomplete="genero" />
        <x-input-error class="mt-2" :messages="$errors->get('genero')" />
        </div>
        --}}

        <div class="mb-3">
            <label for="genero_id" class="form-label"><strong>Gênero</strong></label>
            <select type="text" class="form-select" id="genero_id" name="genero_id">
                <option value="">--- Selecione ---</option>
                @foreach($generos as $item)
                <option value="{{ $item->id }}" {{ isset($user) && $item->id === $user->genero ? "selected='selected'": "" }}>{{ $item->titulo }}</option>

                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data Nascimento</label>
            <input id="data_nascimento" name="data_nascimento" type="date" class="form-control" value="{{ $user->data_nascimento ?? old('data_nascimento')}}" required autocomplete="data_nascimento" />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <div class="mb-3">
            <label for="logradouro" class="form-label">Logradouro</label>
            <input id="logradouro" name="logradouro" type="text" class="form-control" value="{{ $user->logradouro ?? old('logradouro')}}" required autocomplete="logradouro" />
            <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
        </div>

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input id="endereco" name="endereco" type="text" class="form-control" value="{{ $user->endereco ?? old('endereco')}}" required autocomplete="endereco" />
            <x-input-error class="mt-2" :messages="$errors->get('endereco')" />
        </div>

        <div class="mb-3">
            <label for="rua" class="form-label">Rua</label>
            <input id="rua" name="rua" type="text" class="form-control" value="{{ $user->rua ?? old('rua')}}" required autocomplete="rua" />
            <x-input-error class="mt-2" :messages="$errors->get('rua')" />
        </div>

        <div class="mb-3">
            <label for="bairro" class="form-label">Bairro</label>
            <input id="bairro" name="bairro" type="text" class="form-control" value="{{ $user->bairro ?? old('bairro')}}" required autocomplete="bairro" />
            <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
        </div>

        <div class="mb-3">
            <label for="numero" class="form-label">Número</label>
            <input id="numero" name="numero" type="text" class="form-control" value="{{ $user->numero ?? old('numero')}}" required autocomplete="numero" />
            <x-input-error class="mt-2" :messages="$errors->get('numero')" />
        </div>

        <div class="mb-3">
            <label for="Cidade" class="form-label">Cidade</label>
            <input id="cidade" name="cidade" type="text" class="form-control" value="{{ $user->cidade ?? old('cidade')}}" required autocomplete="cidade" />
            <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input id="estado" name="estado" type="text" class="form-control" value="{{ $user->estado ?? old('estado')}}" required autocomplete="estado" />
            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
        </div>

        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input id="complemento" name="complemento" type="text" class="form-control" value="{{ $user->complemento ?? old('complemento')}}" required autocomplete="complemento" />
            <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Salvar') }}</x-primary-button>

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