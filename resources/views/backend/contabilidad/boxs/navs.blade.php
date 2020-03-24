<ul class="nav nav-pills">
  <li class="nav-item"><a class="nav-link @if(Route::is('contabl.salarios')) active @endif" href="{{route('contabl.salarios')}}">Salarios Mes</a></li>
  <li class="nav-item"><a class="nav-link @if(Route::is('contabl.ventas')) active @endif" href="{{route('contabl.ventas')}}">Ventas Mes</a></li>
</ul>
