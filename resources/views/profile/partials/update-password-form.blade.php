<section class="perfil-section">
    <header>
        <h2>{{ __('Atualizar senha') }}</h2>
        <p>{{ __('Garanta que sua conta esteja usando uma senha longa e forte para manter a segurança.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('Senha atual') }}</label>
            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            @if ($errors->updatePassword->has('current_password'))
                <p class="text-error">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Nova senha') }}</label>
            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password'))
                <p class="text-error">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirmar senha') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="text-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex">
            <button type="submit" class="btn-primary">{{ __('Salvar') }}</button>
            @if (session('status') === 'password-updated')
                <p class="text-success">{{ __('Senha atualizada com sucesso.') }}</p>
            @endif
        </div>
    </form>
</section>