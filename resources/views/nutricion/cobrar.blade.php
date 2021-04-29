<form action="{{ url('/admin/citas/chargeAdvanced') }}" method="post" id="chargeDate">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="idDate" id="idDate" value="<?php echo $id ?>">
  <input type="hidden" name="id_rate" value="<?php echo $id_serv ?>">
  <div class="col-xs-12 col-md-6 push-20">
    <div class="box-payment-card">
      <h4>PAGAR AHORA</h4>
      <select class="likeBtn" name="type_payment" id="type_payment" multiple>
        <option value="card" selected="">Tarjeta</option>
        <option value="cash">Efectivo</option>
        <option value="banco">Banco</option>
      </select>
      @include('admin.blocks.stripeBox')
      <div class="text-center">
      <button class="btn btn-lg btn-success sendForm" type="button" data-id="chargeDate">
        Cobrar
      </button>
        </div>
    </div>
  </div>
  <div class="col-xs-12 col-md-6 push-20">
    @include('admin.blocks.stripe-buttons')
  </div>
</form>

