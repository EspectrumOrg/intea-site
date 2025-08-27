
<nav class="container-navbar">
     <div class="content">

         <div class="logo">
             <a href="{{ route('dashboard') }}">INTEA</a>
         </div>

         <div class="links">
             <ul class="ul">
                 @can('visualizar-admin')
                 <li class="nav-item">
                     <a href="{{ route('usuario.index') }}">Admin</a>
                 </li>
                 @endcan
                 <li class="nav-item">
                     <a href="{{ route('dashboard') }}">Dashboard</a>
                 </li>
                 <li class="nav-item">
                     <a href=" ">Postagens</a>
                 </li>
                 <li class="nav-item">
                     <a href="#">Profissionais</a>
                 </li>

                 <li class="nav-item dropdown-item">
                     @if (!empty(Auth::user()->foto))
                     <a href="#"><img src="{{ asset('storage/'.Auth::user()->foto) }}"></a>
                     @else
                     <a href="#"><img src="{{ url('assets/images/logos/contas/user.png') }}" class="card-img-top" alt="foto perfil"></a>
                     @endif

                     <ul class="dropdown">
                         <li><a href="{{ route('profile.edit') }}">Perfil</a></li>
                         <li> <!-- Authentication -->
                             <form method="POST" action="{{ route('logout') }}">
                                 @csrf
                                 <a onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link" href="#">Sair</a>
                             </form>
                         </li>
                     </ul>
                 </li>
             </ul>
         </div>

     </div>
 </nav>