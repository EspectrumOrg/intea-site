<section class="perfil-section">
    <header class="header">
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Editar Dados do Autista') }}
        </h2>
    </header>

    @isset($autista)
    <form method="post" 
          action="{{ route('autistas.update_responsavel', $autista->id) }}" 
          class="mt-6 space-y-6"
          enctype="multipart/form-data"> {{-- Necessário para upload de arquivos --}}
        @csrf
        @method('patch')

        {{-- 🖼️ Campo para upload da foto --}}
        <div class="container-foto-dependente">
            <label for="foto" class="form-label">Foto</label>

            @if ($autista->usuario->foto)
                <div class="foto-dependente">
                    <img src="{{ asset('storage/' . $autista->usuario->foto) }}" 
                         alt="{{ basename($autista->usuario->foto) }}" 
                         width="100" 
                         class="img-thumbnail">
                </div>
            @endif

            <input id="foto" name="foto" type="file" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" 
                   value="{{ $autista->usuario->email ?? old('email') }}" required />
        </div>

        <div class="mb-3">
            <label for="user" class="form-label">Usuário</label>
            <input id="user" name="user" type="text" class="form-control" 
                   value="{{ $autista->usuario->user ?? old('user') }}" required />
        </div>

        <div class="mb-3">
            <label for="apelido" class="form-label">Apelido</label>
            <input id="apelido" name="apelido" type="text" class="form-control" 
                   value="{{ $autista->usuario->apelido ?? old('apelido') }}" />
        </div>

        <div class="mb-3">
            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
            <input id="data_nascimento" name="data_nascimento" type="date" class="form-control" 
                   value="{{ $autista->usuario->data_nascimento ?? old('data_nascimento') }}" />
        </div>


        <div class="flex">
            <button type="submit" class="btn btn-primary">Salvar</button>

            @if (session('status') === 'autista-updated')
                <p class="text-success ms-3">Dados do autista atualizados com sucesso.</p>
            @endif
        </div>
    </form>
    @else
        <p class="text-danger">Erro: autista não encontrado.</p>
    @endisset
</section>
