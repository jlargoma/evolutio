@extends('layouts.popup')
@section('content')
<div class="col-xs-12">
    <div class="col-xs-12 not-padding push-20">
        <h2 class="text-center font-w300">
            COBRO DE <span class="font-w600">{{getMonthSpanish($month,false).' '.$year}}</span> A
            <span class="font-w600"><?php echo strtoupper($user->name); ?></span>
        </h2>
    </div>

    <form class="form-toPayment" method="post" action="{{ url('/admin/cobros/cobrar') }}">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id_user" value="<?php echo $user->id; ?>">
        <input type="hidden" id="importeCobrar" value="<?php echo $rate->price; ?>">
        <input type="hidden" id="fecha_pago" name="fecha_pago" value="<?php echo $year.'-'.$month.'-01'; ?>">
        <input type="hidden" id="id_rate" name="id_rate" value="<?php echo $rate->id; ?>"/>
        <div class="col-xs-12">
            <div class="col-md-12 push-20">
                <h2 class="text-center font-w300">
                    Cuota a cobrar <span class="font-w600"><?php echo $rate->name; ?></span>
                </h2>
                <div class="row push-20">
                    <p class="text-danger text-center font-w600 font-s32"><?php echo $rate->price; ?>€</p>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-6">
                    <label for="type_payment">Forma de pago</label>
                    <select class="form-control" name="type_payment" id="type_payment">
                            <option value="cash">Efectivo</option>
                            <option value="banco">Banco</option>
                            <option value="card">Tarjeta</option>
                    </select>
                </div>
                <div class="col-md-4 col-xs-6 push-20">
                    <label for="discount">DTO %:</label>
                    <input type="number" id="discount" name="discount" class="form-control"/>
                    
                </div>
                <div class="col-md-4 col-xs-6">
                    <label for="importeFinal">Total:</label>
                    <input id="importeFinal" type="text" name="importe" class="form-control"
                               value="<?php echo $rate->price; ?>"/>
                </div>
            </div>
            <div style="clear: both;"></div>
            @include('admin.usuarios.clientes.forms.stripeBox')
            <div style="clear: both;"></div>
            <div class="col-md-12 text-center push-20">
                <div class="col-md-4 col-xs-12">
                    <button class="btn btn-lg btn-success" type="submit" id="submitFormPayment">
                        Cobrar
                    </button>
                </div>
                <div class="col-md-4 col-xs-12">
                    <a class="btn btn-lg btn-success" href="{{ $pStripe}}" target="_black">
                        Link Stripe
                    </a>
                </div>
                <div class="col-md-4 col-xs-12">
                    <a class="btn btn-lg btn-danger"
                       href="{{ url('/admin/rates/unassigned')}}/<?php echo $user->id; ?>/<?php echo $rate->id; ?>/<?php echo $date; ?>">
                        Desasignar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script type="text/javascript">
  jQuery(function () {
    App.initHelpers(['datepicker']);
  });

  $(document).ready(function () {
    $('#discount').change(function (event) {
      var discount = $(this).val();
      var importe = $('#importeCobrar').val();
      var percent = discount / 100;

      $('#importeFinal').val(importe - (importe * percent));

    });

    $('#type_payment').change(function (e) {
        var value = $("#type_payment option:selected").val();
        if (value == "card") {
            $('#stripeBox').show();
            $('.form-toPayment').attr('id', 'paymentForm');
        } else {
            $('#stripeBox').hide();
            $('.form-toPayment').removeAttr('id');
        }

    });
    <?php if ($card):?>
        $('#card-element').hide();
        $('#changeCreditCard').on('click', function(){
            $('#cardExists').hide();
            $('#cardLoaded').val(0);
            $('#card-element').show();
            $(".new_cc").prop('required',true);
        });
    <?php endif;?>
  });
</script>
@endsection