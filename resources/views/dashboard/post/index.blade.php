 <!-- Conteúdo principal com scroll -->
 <div class="container-post">
     <div class="create-post">
         @include("dashboard.post.create")
     </div>

     <div class="content-post">
         @foreach($postagens as $postagem)
         <div class="corpo-post">

             <div class="topo"> <!-- info conta -->
                 <div class="foto-perfil">
                     <a href="#"><img src="{{ asset('storage/'.$postagem->usuario->foto) }}" alt="foto perfil"></a>
                 </div>

                 <div class="info-perfil">
                     <h1>{{ Str::limit($postagem->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                     <h2>{{ Str::limit($postagem->usuario->descricao ?? '--', 75, '...') }}</h2>
                     <h3>{{ $postagem->created_at->format('d/m/y') }}</h3>
                 </div>
             </div>

             <div class="conteudo-post"> <!-- conteudo postagem -->
                 <div class="coment-perfil">
                     <p class="texto-curto" id="texto-{{ $postagem->id }}">
                         {{ Str::limit($postagem->texto_postagem, 150, '') }}
                         @if (strlen($postagem->texto_postagem) > 150)
                         <span class="mostrar-mais" onclick="toggleTexto('{{ $postagem->id }}', this)">...mais</span>
                         @endif
                     </p>

                     <p class="texto-completo" id="texto-completo-{{ $postagem->id }}" style="display: none;">
                         {{ $postagem->texto_postagem }}
                         <span class="mostrar-mais" onclick="toggleTexto('{{ $postagem->id }}', this)">...menos</span>
                     </p>


                 </div>

                 <div class="image-post">
                     @if ($postagem->imagens->isNotEmpty())
                     <img src="{{ asset('storage/'.$postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                     @endif
                 </div>
             </div>


             <div class="opcoes-perfil"> <!-- interações postagem -->

                 <hr>

                 <div class="options">
                     <a> Comentarios </a>
                     <a> Reagir </a>
                     <a> Compartilhar </a>
                 </div>
             </div>
         </div>
         @endforeach
     </div>
 </div>