 
<?php
$url = substr(Request::path(), 6);
$posicion = strpos($url, '/');
if ($posicion > 0) {
  $url = substr($url, 0, $posicion);
} else {
  $url;
};
?>


<div class="col-md-12 col-xs-12 push-20">
  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "ingresos"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >Ingresos</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/ingresos/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%;">
          Ingresos
        </button>
      </a>
    <?php endif ?>	
  </div>


  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "gastos"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >Gastos</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/gastos/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%;">
          Gastos
        </button>
      </a>
    <?php endif ?>	
  </div>

  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "pending"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >Banco</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/pending/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%;">
          Banco
        </button>
      </a>
    <?php endif ?>
  </div>

  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "cashbox"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >Caja</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/cashbox/')}}" >
        <button class="btn btn-md btn-primary" style="width: 100%;">
          Caja
        </button>
      </a>
    <?php endif ?>
  </div>

  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "perdidas-ganancias"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >CTA P &amp; G</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/perdidas-ganancias/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%;">
          CTA P &amp; G
        </button>
      </a>
    <?php endif ?>
  </div>
  <div class="col-md-1 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "cuenta-socios"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >CTA SOCIOS</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/cuenta-socios/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%; font-size: 13px">
          CTA SOCIOS
        </button>
      </a>
    <?php endif ?>
  </div>
  <div class="col-md-2 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "salario-mes"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >SALARIOS MES</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/salario-mes/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%; font-size: 13px">
          SALARIOS MES
        </button>
      </a>
    <?php endif ?>
  </div>
  <div class="col-md-2 col-xs-4 push-10" style="padding: 5px;">
    <?php if ($url == "ventas-mes"): ?>
      <button class="btn btn-md" style="width: 100%; background-color: #6600ff;pointer-events: none" disabled>
        <a class="text-white" >VENTAS MES</a>
      </button>
    <?php else: ?>
      <a class="text-white" href="{{url('/admin/ventas-mes/')}}">
        <button class="btn btn-md btn-primary" style="width: 100%; font-size: 13px">
          VENTAS MES
        </button>
      </a>
    <?php endif ?>
  </div>
</div>
