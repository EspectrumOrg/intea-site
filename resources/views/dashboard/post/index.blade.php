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

                 <div class="acoes-perfil">
                     <button class="seguir-btn">+Seguir</button>

                     <div class="dropdown">
                         <button class="menu-opcoes">...</button>
                         <ul class="dropdown-content">
                             <li><a href="#">Editar</a></li>
                             <li><a href="#">Excluir</a></li>
                             <li><a href="#">Denunciar</a></li>
                         </ul>
                     </div>
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

                 <div class="dados-post">
                     <h1>({{ $postagem->curtidas_count }}) curtidas</h1>
                     <h1>({{ $postagem->comentarios_count }}) comentários</h1>
                 </div>
             </div>


             <div class="acoes-post">
                 <div class="options">

                     <div class="botoes">
                         <div class="botao">
                             <button type="button" onclick="toggleForm('{{ $postagem->id }}')">
                                 <span class="material-symbols-outlined">chat</span>
                                 Comentar
                             </button>
                         </div>


                         <form method="POST" action="{{ route('post.curtida', $postagem->id) }}">
                             @csrf
                             <button type="submit" style="background: none; border: none; cursor: pointer;">
                                 {!! $postagem->curtidas_usuario
                                 ? '<span class="material-symbols-outlined" style="color:red;">favorite</span>Curtido'
                                 : '<span class="material-symbols-outlined">favorite</span>Curtir'
                                 !!}
                             </button>
                         </form>

                     </div>

                     <div class="comentario-post" id="form-comentario-{{ $postagem->id }}" style="display: none;">
                         <form method="POST" action="{{ route('post.comentario', $postagem->id) }}">
                             @csrf
                             <input type="text" name="comentario" placeholder="Adicionar comentário" required>
                             <div style="display: flex; justify-content: end;">
                                 <button type="submit">Comentar</button>
                             </div>
                         </form>
                     </div>

                     <div class="lista-comentarios" id="comentarios-{{ $postagem->id }}">
                         @foreach($postagem->comentarios as $index => $comentario)
                         <div class="comentario {{ $index >= 2 ? 'hidden' : '' }}">
                             <div class="info-perfil">
                                 <a href="#">
                                     <img src="{{ asset('storage/'.$comentario->usuario->foto) }}" alt="foto perfil">
                                 </a>
                                 <div class="info">
                                     <h1>{{ $comentario->usuario->user }}</h1>
                                     <h2>{{ Str::limit($comentario->usuario->descricao ?? '--', 75, '...') }}</h2>
                                     <span class="tempo">{{ $comentario->created_at->diffForHumans() }}</span>
                                 </div>
                             </div>
                             <p class="texto-comentario">{{ $comentario->comentario}}</p>
                         </div>
                         @endforeach
                     </div>


                     @if($postagem->comentarios->count() > 2)
                     <button type="button" class="carregar-mais" onclick="carregarMais('{{ $postagem->id }}')">
                         Carregar mais
                     </button>
                     @endif

                 </div>
             </div>
         </div>
         @endforeach
     </div>
 </div>