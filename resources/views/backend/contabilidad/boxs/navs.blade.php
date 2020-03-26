<ul class="nav nav-pills">
  <li class="nav-item">
    <a class="nav-link @if(Route::is('contabl')) active @endif" href="{{route('contabl')}}">
      Estadísticas
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Route::is('contabl.salarios')) active @endif" href="{{route('contabl.salarios')}}">
      Salarios Mes
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link @if(Route::is('contabl.ingresos')) active @endif" href="{{route('contabl.ingresos')}}">
      Ingresos
    </a>
  </li>
</ul>
