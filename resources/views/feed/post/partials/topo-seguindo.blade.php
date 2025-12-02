<!-- layout geral -->
<div class="seguindo-container">
    <div class="feed-navigation">
        <a href="{{ route('post.index') }}" class="nav-feed {{ request()->routeIs('post.index') ? 'active' : '' }}">
            <span class="material-symbols-outlined">public</span>
            Principal
        </a>
        <a href="{{ route('post.seguindo') }}" class="nav-feed {{ request()->routeIs('post.seguindo') ? 'active' : '' }}">
            <span class="material-symbols-outlined">group</span>
            Seguindo
        </a>
        @if(isset($interessesUsuario) && $interessesUsuario->count() > 0)
        <a href="{{ route('post.personalizado') }}" class="nav-feed {{ request()->routeIs('post.personalizado') ? 'active' : '' }}">
            Personalizado
        </a>
        @endif
        @if(isset($interessesUsuario))
        @foreach($interessesUsuario as $interesse)
        <a href="{{ route('post.interesse', $interesse->slug) }}"
            class="nav-feed {{ request()->routeIs('post.interesse') && request()->route('slug') == $interesse->slug ? 'active' : '' }}"
            style="color: {{ $interesse->cor }}">
            <!-- CORREÇÃO COM FALLBACK: Se imagem falhar, mostra ícone Material -->
            @if($interesse->icone_custom)
                <span class="icon-container" style="display: inline-block; position: relative; width: 16px; height: 16px; margin-right: 4px;">
                    <img src="{{ $interesse->icone }}" 
                         alt="{{ $interesse->nome }}"
                         style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                    <span class="material-symbols-outlined" style="display: none; font-size: 16px; color: {{ $interesse->cor }};">tag</span>
                </span>
            @else
                <span class="material-symbols-outlined" style="color: {{ $interesse->cor }};">{{ $interesse->icone ?? 'tag' }}</span>
            @endif
            {{ $interesse->nome }}
        </a>
        @endforeach
        @endif
    </div>
</div>