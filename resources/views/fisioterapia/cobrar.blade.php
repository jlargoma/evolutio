<form action="{{ url('/admin/citas/chargeAdvanced') }}" method="post" id="chargeDate">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="idDate" id="idDate" value="<?php echo $id ?>">
  <input type="hidden" name="id_rate" value="<?php echo $id_serv ?>">
  @if($id>0)
  <div class="col-xs-12 col-md-6 push-20">
    <div class="col-xs-6">
      <label for="type">Tipo de cobro</label>
      <?php $old = old('type_payment'); ?>
      <select class=" form-control" id="type_payment" name="type" style="width: 100%;" placeholder="Seleccione acción" required >
        <option value="card" <?php if ($old == 'card') echo 'selected'; ?>>Tarjeta</option>
        <option value="inv" <?php if ($old == 'inv') echo 'selected'; ?>>Invitado</option>
        <option value="cash" <?php if ($old == 'cash') echo 'selected'; ?>>Efectivo</option>
        <option value="banco" <?php if ($old == 'banco') echo 'selected'; ?>>Banco</option>
      </select>
    </div>
    <div class="col-xs-6">
      <label for="">Precio</label>
      <input type="number" class="only-numbers form-control" id="priceRate" name="importe" value="{{$price}}">
    </div>
    @include('admin.blocks.stripe-buttons')
  </div>
  <div class="col-xs-12 col-md-6 push-20" style=" margin-top: -2.3em;">
    @include('admin.blocks.stripeBox')
  </div>
  @else
  <div class="col-xs-12 col-md-12 my-1">
    <label for="">Precio</label>
    <input type="number" class="only-numbers form-control" id="priceRate" name="importe" value="{{$price}}">
  </div>
  @endif
</form>

