<form action="{{ url('/admin/citas/chargeAdvanced') }}" method="post" id="chargeDate">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="idDate" id="idDate" value="<?php echo $id ?>">
  <input type="hidden" name="id_rate" value="<?php echo $id_serv ?>">
  <div class="col-xs-12 col-md-6 push-20">
    <div class="box-payment-card">
      <h4>PAGAR AHORA</h4>
      <div class="row">
        <div class="col-xs-9">
          <select class="likeBtn" name="type_payment" id="type_payment" multiple>
            <option value="card" selected="">Tarjeta</option>
            <option value="cash">Efectivo</option>
            <option value="banco">Banco</option>
            <option value="bono">Bonos</option>
          </select>
        </div>
        <div class="col-xs-3">
          <button class="btn btn-lg btn-success sendForm" type="button" data-id="chargeDate">
            Cobrar
          </button>
        </div>
        <div class="col-xs-12">
        @include('admin.blocks.stripeBox')
        <div id="bonosBox" style="display: none;">
          <?php 
          $oBonoLst = $oUser->bonos;
          if (count($oBonoLst)>0){
            $bNames = [];
            foreach ($oBonoLst as $b){
              if ($b->charges_to) continue;
              if (!isset($bNames[$b->id_bono]))
                $bNames[$b->id_bono] = $b->bono->name;
              ?>
              <div class="col-md-6 col-xs-12">
                <div class="checkBono" >
                <input type="checkbox" name="id_bono[]" value="{{$b->id}}" class="form-control"/>
                <label>{{$bNames[$b->id_bono]}}: <b>{{moneda($b->price)}}</b></label>
                </div>
              </div>
              <?php
            }
          } else {
            ?>
            <p class="alert alert-warning">No tiene bonos asignados</p>
            <?php
          }
          ?>
            <button class="btn btn-default add_bono" data-idUser="<?php echo $oUser->id; ?>" type="button">
                <i class="fa fa-usd" aria-hidden="true"></i> Comprar
            </button>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-md-6 push-20">
    @include('admin.blocks.stripe-buttons')
  </div>
</form>

<script type="text/javascript">
$(document).ready(function () {
    $('#type_payment').change(function (e) {
        var value = $("#type_payment option:selected").val();
        if (value == "bono") {
            $('#bonosBox').show();
            $('#stripeBox').hide();
        } else {
            $('#bonosBox').hide();
            $('#stripeBox').show();
        }

    });
    
  $('input[type=checkbox][name=id_bono]').on('change', function() {
    if ($(this).prop('checked'))  $(this).closest('.checkBono').addClass('active');
    else   $(this).closest('.checkBono').removeClass('active');
  });
  $('.add_bono').on('click', function(e) {
    e.preventDefault();
    var id_user = $(this).attr('data-idUser');
    var back = '/{{$type}}/{{$id}}'
    location.href = '/admin/bonos/comprar/' + id_user + back;
  });
});
</script>
<style>
button.btn.btn-default.add_bono {
    clear: both;
    overflow: auto;
    width: 90%;
    border: 1px solid #45c37b;
    margin: 12px 5%;
}
</style>
        