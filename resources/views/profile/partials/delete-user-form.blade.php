<section class="perfil-section">
    <header>
        <h2>{{ __('Excluir Conta') }}</h2>
        <p>{{ __('Ao excluir sua conta, todos os seus dados serão removidos permanentemente. Baixe quaisquer informações importantes antes de prosseguir.') }}</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <div class="mb-3">
            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Digite sua senha para confirmar') }}" />
            @if ($errors->userDeletion->has('password'))
                <p class="text-error">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <div class="flex">
            <button type="submit" id="botao-excluir" class="btn-primary">
                {{ __('Excluir Conta') }}
            </button>
        </div>
    </form>
</section>