<?php
$path = Request::path();
$oUser = Auth::user();
$uRole = $oUser->role;

?>
<ul class="nav-main">
  <li class="{{ $path == 'admin/clientes' ? 'active' : '' }}">
    <a href="{{ url('/admin/clientes') }}" >
      <i class="fa fa-users"></i><span class="sidebar-mini-hide font-w600">Clientes</span>
    </a>
  </li>
  <li class="{{ (str_contains($path,'citas-pt')) ? 'active' : '' }}">
    <a href="{{ url('/admin/citas-pt') }}" >
      <i class="fa fa-calendar-o"></i><span class="sidebar-mini-hide font-w600">P.T.</span>
    </a>
  </li>
  <li class="{{ (str_contains($path,'citas-nutricion')) ? 'active' : '' }}">
    <a href="{{ url('/admin/citas-nutricion') }}" >
      <i class="fa fa-calendar-o"></i><span class="sidebar-mini-hide font-w600">NUTRICIÓN</span>
    </a>
  </li>
  <li class="{{ (str_contains($path,'citas-fisioterapia/')) ? 'active' : '' }}">
    <a href="{{ url('/admin/citas-fisioterapia') }}" >
      <i class="fa fa-calendar-o"></i><span class="sidebar-mini-hide font-w600">FISIOTERAPIA</span>
    </a>
  </li>
  <li class="{{ (str_contains($path,'citas-fisioterapia-getafe')) ? 'active' : '' }}">
    <a href="{{ url('/admin/citas-fisioterapia-getafe') }}" >
      <i class="fa fa-calendar-o"></i><span class="sidebar-mini-hide font-w600">FISIOTERAPIA Getafe</span>
    </a>
  </li>
  <li class="{{ (str_contains($path,'citas-estetica')) ? 'active' : '' }}">
    <a href="{{ url('/admin/citas-estetica') }}" >
      <i class="fa fa-calendar-o"></i><span class="sidebar-mini-hide font-w600">ESTÉTICA</span>
    </a>
  </li>
  <li class="{{ $path == 'admin/tarifas' ? 'active' : '' }}">
    <a href="{{url('/admin/tarifas/listado')}}" class="font-w600"><i class="fa fa-thumb-tack"></i> <span class="sidebar-mini-hide font-w600">Servicios</span></a>
  </li>
  <li class="{{ $path == 'admin/bonos' ? 'active' : '' }}">
    <a href="{{url('/admin/bonos/listado')}}" class="font-w600"><i class="fa fa-thumb-tack"></i> <span class="sidebar-mini-hide font-w600">Bonos</span></a>
  </li>
  @if($uRole == "admin")
  <li class="{{ str_contains($path,'admin/entrenadores') ? 'active' : '' }}">
    <a href="{{ url('/admin/entrenadores/activos') }}" >
      <i class="fa fa-hand-rock-o"></i><span class="sidebar-mini-hide font-w600">SUELDOS Y SALARIOS</span>
    </a>
  </li>
  <li class="{{ $path == 'admin/usuarios' ? 'active' : '' }}">
    <a href="{{ url('/admin/usuarios') }}" >
      <i class="fa fa-users"></i><span class="sidebar-mini-hide font-w600">Usuarios</span>
    </a>
  </li>
  <li class="{{ $path == 'admin/ingresos' ? 'active' : '' }}">
    <a href="{{url('/admin/ingresos/')}}" class="font-w600"><i class="fa fa-line-chart"></i> <span class="sidebar-mini-hide font-w600">Contabilidad</span></a>
  </li>
  <li class="{{ $path == 'admin/facturas' ? 'active' : '' }}">
    <a href="{{url('/admin/facturas/')}}" class="font-w600"><i class="fa fa-files-o"></i> <span class="sidebar-mini-hide font-w600">Facturas</span></a>
  </li>

  <li class="subMenu <?php echo ($path == 'admin/manual/bonos' || $path == 'admin/manual/citas') ? 'opened' : 'open' ?>">
    <i class="fa fa-files-o" style="margin-right: 15px;"></i> <span class="sidebar-mini-hide font-w600 ">  Manuales</span>
    <ul style="display:none">
      <li class="{{ $path == 'admin/manual/bonos' ? 'active' : '' }}">
        <a href="{{url('/admin/manual/bonos')}}" class="font-w600"><i class="fa fa-files-o"></i> <span class="sidebar-mini-hide font-w600">Manual Bonos</span></a>
      </li>
      <li class="{{ $path == 'admin/manual/citas' ? 'active' : '' }}">
        <a href="{{url('/admin/manual/citas')}}" class="font-w600"><i class="fa fa-files-o"></i> <span class="sidebar-mini-hide font-w600">Manual citas</span></a>
      </li>
    </ul>
  </li>
  <li class="{{ $path == 'control-contabilidad' ? 'active' : '' }}">
    <a href="{{url('/control-contabilidad')}}" class="font-w600"><i class="fa fa-line-chart"></i> <span class="sidebar-mini-hide font-w600">Control Contabilidad</span></a>
  </li>
  <li class="{{ (str_contains($path,'settings_msgs')) ? 'active' : '' }}">
    <a href="{{url('/admin/settings_msgs')}}" class="font-w600"><i class="fa fa-building"></i> <span class="sidebar-mini-hide font-w600">Txt Mails</span></a>
  </li>

  <li class="subMenu <?php echo str_contains($path,'admin/convenio') ? 'opened' : 'open' ?>">
    <i class="fa fa-files-o" style="margin-right: 15px;"></i> <span class="sidebar-mini-hide font-w600 ">  Convenios</span>
    <ul>
    <li class="<?php echo str_contains($path,'convenios/listado') ? 'active' : '' ?>">
       <a href="{{url('/admin/convenios/listado')}}" class="font-w600"><i class="fa fa-thumb-tack"></i> <span class="sidebar-mini-hide font-w600">Convenios</span></a>
      </li>
      <li class="<?php echo str_contains($path,'admin/convenios/informes') ? 'active' : '' ?>">
        <a href="{{url('/admin/convenios/informes/')}}" class="font-w600"><i class="fa fa-files-o"></i> <span class="sidebar-mini-hide font-w600">Informes</span></a>
      </li>
    </ul>
  </li>
  
  @endif
  
  @if($uRole == "administrativo")
  
  <li class="{{ $path == 'contabilidad' ? 'active' : '' }}">
    <a href="{{url('/departamento/contabilidad')}}" class="font-w600"><i class="fa fa-line-chart"></i> <span class="sidebar-mini-hide font-w600">Contabilidad</span></a>
  </li>
  <li class="{{ $path == 'informes-cliente' ? 'active' : '' }}">
    <a href="{{url('/departamento/informes-cliente')}}" class="font-w600"><i class="fa fa-line-chart"></i> <span class="sidebar-mini-hide font-w600">Clientes del mes</span></a>
  </li>
    @if($oUser->id == 3370)
    <li class="subMenu <?php echo str_contains($path,'admin/convenio') ? 'opened' : 'open' ?>">
      <i class="fa fa-files-o" style="margin-right: 15px;"></i> <span class="sidebar-mini-hide font-w600 ">  Convenios</span>
      <ul>
      <li class="<?php echo str_contains($path,'convenios/listado') ? 'active' : '' ?>">
        <a href="{{url('/admin/convenios/listado')}}" class="font-w600"><i class="fa fa-thumb-tack"></i> <span class="sidebar-mini-hide font-w600">Convenios</span></a>
        </li>
        <li class="<?php echo str_contains($path,'admin/convenios/informes') ? 'active' : '' ?>">
          <a href="{{url('/admin/convenios/informes/')}}" class="font-w600"><i class="fa fa-files-o"></i> <span class="sidebar-mini-hide font-w600">Informes</span></a>
        </li>
      </ul>
    </li>
    @endif

  @endif
  <li style="margin-left: 17px;">
    <form action="{{ url('/logout') }}" method="POST">
      @csrf
      <button alt="Salir" class="text-danger" style="color: #d26a5c;" type="submit">
        <i class="fa fa-btn fa-sign-out text-danger"></i>  <span class="sidebar-mini-hide font-w600">Salir ({{ Auth::user()->name }})</span><!-- -->
      </button>
    </form>
  </li>
</ul>
<style>
  .subMenu{
    display: block;
    padding: 10px 20px;
    color: rgba(255, 255, 255, 1);
    text-transform: uppercase;
    cursor: pointer;
  }
  .subMenu ul{
    overflow: auto;
    height: auto;
    background-color: transparent;
    padding-left: 24px;
  }
  .subMenu.opened ul{
    display: block !important;
  }
  .nav-main .subMenu ul > li {
    opacity: 1;
  }
  .nav-main .subMenu ul > li a{
    padding: 7px;
    margin: 8px 0;
  }
</style>
