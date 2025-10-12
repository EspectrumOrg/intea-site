<!-- style -->
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">

    <div class="container">
        <h1 class="titulo">O que está bombando</h1>

        <div class="posts">
            @foreach($posts as $post)
            <div class="post">
                <p>{{ Str::limit($post->texto_postagem, 80, '...') }}</p>
                <span class="likes">❤ {{ $post->curtidas_count }} curtidas</span>
            </div>
            @endforeach
        </div>
    </div>