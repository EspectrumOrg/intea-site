<section class="perfil-section">
    <header>
        <h2>{{ __('Opções de Privacidade') }}</h2>
        <p>{{ __('Configurações para ter mais segurança') }}</p>
    </header>

    <form method="POST" action="{{ route('usuario.update_privacidade') }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="visibilidade" class="form-label">{{ __('Visibilidade da conta') }}</label>

            <select id="visibilidade" name="visibilidade" class="form-control" required>
                <option value="1" {{ (string) old('visibilidade', auth()->user()->visibilidade) === '1' ? 'selected' : '' }}>
                    Público (Posts visíveis para todos)
                </option>
                <option value="0" {{ (string) old('visibilidade', auth()->user()->visibilidade) === '0' ? 'selected' : '' }}>
                    Privado (Posts visíveis apenas para seguidores)
                </option>
            </select>

            @if ($errors->has('visibilidade'))
                <p class="text-error">{{ $errors->first('visibilidade') }}</p>
            @endif
        </div>

        <div class="flex">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
            @if(session('success'))
                <p class="text-success">{{ session('success') }}</p>
            @endif
        </div>
    </form>
</section>