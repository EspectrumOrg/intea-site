<section class="perfil-section">
    <header class="header">

        <div class="foto-perfil">
            @if (!empty($user->foto))
            <img src="{{ asset('storage/'.$user->foto) }}" class="card-img-top" alt="foto perfil">
            @else
            <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil">
            @endif
        </div>

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

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

 
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
            <label for="foto" class="form-label">Foto Perfil</label>
            <input id="foto" name="foto" type="file" class="form-control" accept="image/*" value="{{ $user->foto ?? old('foto')}}" autocomplete="foto">
            <x-input-error class="mt-2" :messages="$errors->get('foto')" />
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="4" cols="50">{{ $user->descricao ?? old('descricao') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('descricao')" />
        </div>

        <div class="mb-3">
            <label for="apelido" class="form-label">Apelido</label>
            <input id="apelido" name="apelido" type="text" class="form-control" value="{{ $user->apelido ?? old('apelido')}}" autocomplete="apelido" />
            <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome User</label>
            <input id="user" name="user" type="text" class="form-control" value="{{ $user->user ?? old('user')}}" required autocomplete="user" />
            <x-input-error class="mt-2" :messages="$errors->get('user')" />
        </div>

        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de Conta</label>
            <input id="tipo_usuario" name="tipo_usuario" type="text" class="form-control"
                value="@switch($user->tipo_usuario)
                @case(1) Admin @break
                @case(2) Autista @break
                @case(3) Comunidade @break
                @case(4) Profissional de Saúde @break
                @case(5) Responsável @break
               @endswitch"
                readonly />
        </div>

        {{-- adicionar campos específicos para cada tipo de conta (tirando admin e comunidade)--}}
        @if ($user->tipo_usuario === 2 && $dadosespecificos)
        <div class="mb-3">
            <label for="cipteia_autista" class="form-label"><strong>Autista</strong></label>
            <input id="cipteia_autista" name="cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->cipteia_autista ?? old('cipteia_autista')}}" required autocomplete="cipteia_autista" />
        </div>
        <div class="mb-3">
            <label for="status_cipteia_autista" class="form-label"><strong>Status Cipteia Autista</strong></label>
            <input id="status_cipteia_autista" name="status_cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->status_cipteia_autista ?? old('status_cipteia_autista')}}" required autocomplete="status_cipteia_autista" />
        </div>
        <div class="mb-3">
            <label for="rg_autista" class="form-label"><strong>RG Autista</strong></label>
            <input id="rg_autista" name="rg_autista" type="text" class="form-control" value="{{ $dadosespecificos->rg_autista ?? old('rg_autista')}}" required autocomplete="rg_autista" />
        </div>
        @if (isset($dadosespecificos->responsavel_id))
        <div class="mb-3">
            <label for="responsavel" class="form-label"><strong>Responsável</strong></label>
            <h1 value="{{ $dadosespecificos->responsavel_id ?? old('responsavel_id')}}" required autocomplete="responsavel_id"></h1>
        </div>
        @endif
        @endif

        @if ($user->tipo_usuario === 4 && $dadosespecificos)
        <div class="mb-3">
            <label for="tipo_registro" class="form-label"><strong>Tipo Registro</strong></label>
            <input id="tipo_registro" name="tipo_registro" type="text" class="form-control" value="{{ $dadosespecificos->tipo_registro ?? old('tipo_registro')}}" required autocomplete="tipo_registro" />
        </div>
        <div class="mb-3">
            <label for="registro_profissional" class="form-label"><strong>Registro Profissional</strong></label>
            <input id="registro_profissional" name="registro_profissional" type="text" class="form-control" value="{{ $dadosespecificos->registro_profissional ?? old('registro_profissional')}}" required autocomplete="registro_profissional" />
        </div>
        @endif

        @if ($user->tipo_usuario === 4 && $dadosespecificos)
        <div class="mb-3">
            <label for="cipteia_autista" class="form-label"><strong>Cipteia Autista</strong></label>
            <input id="cipteia_autista" name="cipteia_autista" type="text" class="form-control" value="{{ $dadosespecificos->cipteia_autista ?? old('cipteia_autista')}}" required autocomplete="cipteia_autista" />
        </div>
        @endif

        <!--
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input id="cpf" name="cpf" type="text" class="form-control" value="{{ $user->cpf ?? old('cpf')}}" required autocomplete="cpf" />
            <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
        </div>
-->

        <div class="mb-3">
            <label for="genero_id" class="form-label">Gênero</label>
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

      <!--  <div class="mb-3">
            <label for="logradouro" class="form-label">Logradouro</label>
            <input id="logradouro" name="logradouro" type="text" class="form-control" value="{{ $user->logradouro ?? old('logradouro')}}" autocomplete="logradouro" />
            <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
        </div> 

        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <input id="endereco" name="endereco" type="text" class="form-control" value="{{ $user->endereco ?? old('endereco')}}" autocomplete="endereco" />
            <x-input-error class="mt-2" :messages="$errors->get('endereco')" />
        </div>

        <div class="mb-3">
            <label for="rua" class="form-label">Rua</label>
            <input id="rua" name="rua" type="text" class="form-control" value="{{ $user->rua ?? old('rua')}}" autocomplete="rua" />
            <x-input-error class="mt-2" :messages="$errors->get('rua')" />
        </div>

        <div class="mb-3">
            <label for="bairro" class="form-label">Bairro</label>
            <input id="bairro" name="bairro" type="text" class="form-control" value="{{ $user->bairro ?? old('bairro')}}" autocomplete="bairro" />
            <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
        </div>

        <div class="mb-3">
            <label for="numero" class="form-label">Número</label>
            <input id="numero" name="numero" type="text" class="form-control" value="{{ $user->numero ?? old('numero')}}" autocomplete="numero" />
            <x-input-error class="mt-2" :messages="$errors->get('numero')" />
        </div>

        <div class="mb-3">
            <label for="Cidade" class="form-label">Cidade</label>
            <input id="cidade" name="cidade" type="text" class="form-control" value="{{ $user->cidade ?? old('cidade')}}" autocomplete="cidade" />
            <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input id="estado" name="estado" type="text" class="form-control" value="{{ $user->estado ?? old('estado')}}" autocomplete="estado" />
            <x-input-error class="mt-2" :messages="$errors->get('estado')" />
        </div>

        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input id="complemento" name="complemento" type="text" class="form-control" value="{{ $user->complemento ?? old('complemento')}}" autocomplete="complemento" />
            <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
        </div>
   -->
       
   <!--
    <h6>Telefones</h6>

        @foreach($telefones as $index => $telefone)
        <div class="mb-3">
            <label for="telefone_{{ $index }}" class="form-label">Telefone {{ $index + 1 }}</label>
            <input id="telefone_{{ $index }}" name="numero_telefone[]" type="tel" class="form-control" value="{{ $telefone->numero_telefone ?? old('numero_telefone.' . $index)}}" required autocomplete="telefone" />
            <x-input-error class="mt-2" :messages="$errors->get('numero_telefone.' . $index)" />
        </div>
        @endforeach
    -->

        <div class="flex">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
            @if (session('status') === 'profile-updated')
            <p class="text-success">{{ __('Informações atualizadas com sucesso.') }}</p>
            @endif
        </div>
    </form>
</section>