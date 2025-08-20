 <!-- Conteúdo principal com scroll -->
 <div class="container-post">
     <div class="create-post">
         @include("dashboard.post.create")
     </div>

     <div class="content-post">
         @foreach($postagens as $postagem)
         <div class="corpo-post">
             <h5>{{ $postagem->created_at->format('d/m/y') }}</h5>
             <p>{{ $postagem->usuario->nome ?? 'Desconhecido' }}</p>
             <div class="conteudo-post">
                 @if ($postagem->imagens->isNotEmpty())
                 <img src="{{ asset('storage/'.$postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                 @endif

                 <div class="coment-perfil">
                     <p class="texto-curto" id="texto-{{ $postagem->id }}">
                         {{ Str::limit($postagem->texto_postagem, 150, '...') }}
                     </p>

                     <p class="texto-completo" id="texto-completo-{{ $postagem->id }}" style="display: none;">
                         {{ $postagem->texto_postagem }}
                     </p>

                     @if (strlen($postagem->texto_postagem) > 150)
                     <button type="button"
                         class="mostrar-mais"
                         onclick="toggleTexto('{{ $postagem->id }}', this)">
                         Mostrar mais
                     </button>
                     @endif
                 </div>

                 <div class="opcoes-perfil">
                     <a> Comentarios </a>
                     <a> Reagir </a>
                     <a> Compartilhar </a>
                 </div>

                 <div class="foto-perfil">
                     <h1>
                         <a> (foto--perfil)Comentar </a>
                     </h1>
                 </div>
             </div>
         </div>
         @endforeach
     </div>
 </div>