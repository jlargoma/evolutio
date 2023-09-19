<?php 
$s_pt = 3;
if(isset($page)){
  $s_pt = 2;
}
if (!isset($datePayment)){
  $datePayment = date('d-m-Y');
}
?>
<form action="{{ url('/admin/ingresos/create') }}" method="post"  id="formNewIncomes" data-ajax="1">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-3 col-xs-12 mb-1em" style="max-height: 30px;">
      <label for="date">Fecha</label>
      <div id="datepicker-component" class="input-group date col-xs-12">
          <input type="text" class="js-datepicker  form-control" name="fecha" id="fecha" value="{{$datePayment}}" style="font-size: 12px" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy"  style="cursor: pointer;" required="">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-xs-12 mb-1em">
      <label for="concept">Concepto</label>
      <input  type="text" class="form-control" name="concept" id="concept" />
    </div>
    <div class="col-lg-2 col-md-3 col-xs-12 mb-1em">
      <label for="import">Importe</label>
      <input  type="number" step="0.01" name="import" id="import" class="form-control" required />
    </div>
    <div class="col-lg-2 col-md-6 col-xs-12 mb-1em">
      <label for="type_payment">Forma de pago</label>
      <select class="form-control" name="type_payment" id="type_payment" >
        <option value="cash">Efectivo</option>
        <option value="card">Tarjeta</option>
        <option value="banco">Banco</option>
        <option value="bono">Bono</option>
        <option value="invita">Invit. Evolutio</option>
      </select>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em">
      <label for="type">Tipo</label>
        <select class="form-control" id="type" name="type"  data-placeholder="Seleccione un tipo" required >
        @foreach($lstRates as $k=>$v)
        <optgroup label="{{$v['n']}}">
            @foreach($v['l'] as $k2=>$v2)
            <option value="{{$k2}}" <?= ($k2 == 30) ? 'selected': '' ?>>{{$v2}}</option>
            @endforeach
        </optgroup>
        @endforeach
        <option value="distribucion">DISTRIBUCION DE BENEFICIOS</option>
      </select>
    </div>
    <div class="col-lg-6  col-md-6 col-xs-12 mb-1em">
      <label for="comment">Observaciones</label>
      <textarea class="form-control" name="comment" id="comment"></textarea>
    </div>
    <div class="col-lg-4  col-md-6 col-xs-12 my-1">
      <button class="btn btn-success" type="submit">Añadir</button>
      <button class="btn btn-secondary" type="button" id="reload">Refrescar Pantalla</button>
    </div>
  </div>
</form>