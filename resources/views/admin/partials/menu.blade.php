 <nav class="navbar navbar-expand-lg bg-primary mb-5" data-bs-theme="dark">
     <div class="container">
         <a class="navbar-brand" href="#">Laravel Intea</a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
             <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
             <ul class="navbar-nav mb-2 mb-lg-0">
                 <li class="nav-item">
                     <a class="nav-link" href=" ">Postagens</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link" href="">Profissionais</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link" href="#">[barra pesquisa]</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link" href="#">Dashboard</a>
                 </li>
                 <li class="nav-item">
                     <!-- Authentication -->
                     <form method="POST" action="#">
                         @csrf

                         <a  
                            onclick="event.preventDefault(); this.closest('form').submit();" 
                            class="nav-link" 
                            href="#"
                        >Sair/conta</a>
                         
                     </form>
                 </li>
             </ul>
         </div>
     </div>
 </nav>