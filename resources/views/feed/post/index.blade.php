 <!-- Conteúdo principal com scroll -->
 <div class="container-post">
     <div class="create-post">
         @include("feed.post.create")
     </div>

     <div class="content-post">
         @foreach($postagens as $postagem)
         <div class="corpo-post">

             <div class="foto-perfil">
                 <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                     @if (!empty($postagem->usuario->foto))
                     <img src="{{ asset('storage/'.$postagem->usuario->foto) }}" alt="foto perfil">
                     @else
                     <img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="sem-foto">
                     @endif
                 </a>
             </div>

             <div class="corpo-content">
                <div class="topo"> <!-- info conta -->
                 <div class="info-perfil">
                     <a href="{{ route('conta.index', ['usuario_id' => $postagem->usuario_id]) }}">
                         <h1>{{ Str::limit($postagem->usuario->user ?? 'Desconhecido', 25, '...') }}</h1>
                     </a>
                     <h2>{{ $postagem->usuario->user }} . {{ $postagem->created_at->shortAbsoluteDiffForHumans() }}</h2>
                 </div>

                 <div class="acoes-perfil">
                     <button class="seguir-btn">Seguir</button>

                     <div class="dropdown"> <!-- opções postagem -->
                         <button class="menu-opcoes">...</button>
                         <ul class="dropdown-content">
                             @if(Auth::id() === $postagem->usuario_id)
                             <li>
                                 <button type="button" class="btn-acao editar" onclick="abrirModalEditar('{{ $postagem->id }}')">
                                     Editar
                                 </button>
                             </li>
                             <li>
                                 <form action="{{ route('post.destroy', $postagem->id) }}" method="POST" style="display:inline">
                                     @csrf
                                     @method('DELETE')
                                     <button type="submit" class="btn-acao excluir">Excluir</button>
                                 </form>
                             </li>
                             @else
                             <li><a href="javascript:void(0)" onclick="abrirModalDenuncia('{{ $postagem->id }}')">Denunciar</a></li>
                             @endif
                         </ul>
                     </div>

                     <!-- Modal Edição dessa postagem -->
                     <div id="modal-editar-{{ $postagem->id }}" class="modal hidden">
                         <div class="modal-content">
                             <button type="button" class="close" onclick="fecharModalEditar('{{ $postagem->id }}')">&times;</button>
                             <div class="modal-content-content">
                                 @include('feed.post.edit', ['postagem' => $postagem])
                             </div>
                         </div>
                     </div>

                     <!-- Modal de denúncia (um para cada postagem) -->
                     <div id="modal-denuncia-postagem-{{ $postagem->id }}" class="modal-denuncia hidden">
                         <div class="modal-content">
                             <span class="close" onclick="fecharModalDenuncia('{{$postagem->id}}')">&times;</span>

                             <form method="POST" style="width: 100%;" action="{{ route('post.denuncia', [$postagem->id, Auth::user()->id]) }}">
                                 @csrf
                                 <div class="form">
                                     <label class="form-label">Motivo Denúncia</label>
                                     <select class="form-select" id="motivo_denuncia" name="motivo_denuncia" required>
                                         <option value="">Tipo</option>
                                         <option value="spam">Spam</option>
                                         <option value="desinformação">Desinformação</option>
                                         <option value="conteudo_explicito">Conteúdo Explícito</option>
                                         <option value="discurso_de_odio">Discurso de Ódio</option>
                                     </select>
                                 </div>

                                 <div class="form-label">
                                     <input class="form-control" name="texto_denuncia" type="text" placeholder="Explique o porquê da denúncia" value="{{ old('texto_denuncia') }}" required autocomplete="off">
                                     <x-input-error class="mt-2" :messages="$errors->get('texto_denuncia')" />
                                 </div>

                                 <div style="display: flex; justify-content: end;">
                                     <button type="submit">Denunciar</button>
                                 </div>
                             </form>
                         </div>
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
                     @if ($postagem->imagens->isNotEmpty() && $postagem->imagens->first()->caminho_imagem)
                     <img src="{{ asset('storage/'.$postagem->imagens->first()->caminho_imagem) }}" class="card-img-top" alt="Imagem da postagem">
                     @endif
                 </div>


                 <div class="dados-post"> <!-- bottom ---------------------------------------------------------------------------------->
                     <h1>
                         <button type="button" onclick="toggleForm('{{ $postagem->id }}')" style="background: none; border: none; cursor: pointer;">
                             <img src="{{ asset('assets/images/logos/symbols/site-claro/coment.png') }}">({{ $postagem->comentarios_count }})
                         </button>
                     </h1>
                     <form method="POST" action="{{ route('post.curtida', $postagem->id) }}">
                         @csrf
                         <h1>
                             <button type="submit" style="background: none; border: none; cursor: pointer;">
                                 <img src="{{ asset('assets/images/logos/symbols/site-claro/' . (!! $postagem->curtidas_usuario ? 'like-preenchido.png' : 'like.png')) }}">({{ $postagem->curtidas_count }})
                             </button>
                         </h1>
                     </form>
                 </div>
             </div>

             <!-- <div class="acoes-post">
                 <div class="options">

                     <div class="botoes">
                         <div class="botao">
                             <button type="button" onclick="toggleForm('{{ $postagem->id }}')">
                                 <span class="material-symbols-outlined">chat</span>
                                 Comentar
                             </button>
                         </div>

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
             </div>-->
         </div>
             </div>

         @endforeach
     </div>
 </div>

 <!-- Modal Criação de postagem -->
 <div id="modal-postar" class="modal hidden">
     <div class="modal-content">
         <button type="button" class="close" onclick="fecharModalPostar()">&times;</button>
         <div class="modal-content-content">
             @include('feed.post.create-modal')
         </div>
     </div>
 </div>