<section class="perfil-section">
    <header>
        <h2>{{ __('Excluir conta') }}</h2>
        <p>{{ __('Ao excluir sua conta, todos os seus dados serão removidos permanentemente.') }}</p>
    </header>

    <form method="POST" action="{{ route('usuario.excluir') }}">
        @csrf
        @method('delete')

        <div class="mb-3">
            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Digite sua senha para confirmar') }}" />
            @if ($errors->userDeletion->has('password'))
                <p class="text-error">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <div style="display: flex; justify-content: end;">
            <button type="submit" id="botao-excluir" class="btn-config-exclude">
                {{ __('Excluir Conta') }}
            </button>
        </div>
    </form>
</section>