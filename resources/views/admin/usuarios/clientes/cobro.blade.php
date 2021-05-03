@extends('layouts.popup')
@section('content')
<div class="content" style="max-width:975px;">
  <div class="col-xs-12 not-padding push-20">
    <h2 class="text-center font-w300">
      COBRO DE <span class="font-w600">{{getMonthSpanish($month,false).' '.$year}}</span> A
      <span class="font-w600"><?php echo strtoupper($user->name); ?></span>
    </h2>
  </div>

  <form class="form-toPayment" method="post" action="{{ url('/admin/cobros/cobrar') }}">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id_uRate" value="<?php echo $uRate; ?>">
    <input type="hidden" id="importeCobrar" value="<?php echo $rate->price; ?>">
    <div class="col-md-12 push-20">
      <h2 class="text-center font-w300">
        Cuota a cobrar <span class="font-w600"><?php echo $rate->name; ?></span> {{moneda($rate->price)}}
      </h2>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <label for="type_payment">Forma de pago</label>
        <select class="likeBtn" name="type_payment" id="type_payment" multiple>
          <option value="card" selected="">Tarjeta</option>
          <option value="cash">Efectivo</option>
          <option value="banco">Banco</option>
        </select>
        @include('admin.blocks.stripeBox')
      </div>
      <div class="col-xs-6 col-md-6">
        <div class="col-md-4 mb-1em">
          <label for="discount">DTO %:</label>
          <input type="number" id="discount" name="discount" class="form-control"/>
        </div>
        <div class="col-md-8">
          <label for="importeFinal">Total:</label>
          <input id="importeFinal" type="text" name="importe" class="form-control"
                 value="<?php echo $importe; ?>"/>
        </div>
        @include('admin.blocks.stripe-actions')
        <div class="col-md-12 mt-2 text-center">
          <div class="col-md-6">
            <button class="btn btn-lg btn-success" type="submit" id="submitFormPayment">
              Cobro directo
            </button>
          </div>
          <div class="col-md-6">
            <a class="btn btn-lg btn-danger"
               href="{{ url('/admin/rates/unassigned')}}/<?php echo $uRate; ?>">
              <i class="fa fa-trash"></i> Desasignar
            </a>
          </div>
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
//            $('#stripeBox').show();
$('#stripeBox').find('.disabled').show();
$('.form-toPayment').attr('id', 'paymentForm');
} else {
//            $('#stripeBox').hide();
$('#stripeBox').find('.disabled').hide();
$('.form-toPayment').removeAttr('id');
}

});
<?php if ($card): ?>
  $('#card-element').hide();
  $('#changeCreditCard').on('click', function () {
  $('#cardExists').hide();
  $('#cardLoaded').val(0);
  $('#card-element').show();
  $(".new_cc").prop('required', true);
  });
<?php endif; ?>


$('.btnStripe').on('click', function () {
var type = $(this).data('t');
var posting = $.post('/admin/send/cobro-mail', {
_token: '{{csrf_token()}}',
u_rate: '{{$uRate}}',
u_email: $('#u_email').val(),
u_phone: $('#u_phone').val(),
discount: $('#discount').val(),
importe: $('#importeFinal').val(),
type: type
});
posting.done(function (data) {
if (data[0] == 'OK') {
  if (type == 'mail') {
    window.show_notif('success', data[1]);
  }
  if (type == 'wsp') {
    if (window.detectMob()) {
      var url = 'whatsapp://send?text=' + encodeURI(data[1]);
    } else {
      var url = 'https://web.whatsapp.com/send?phone=' + $('#u_phone').val() + '&text=' + encodeURI(data[1]);
    }
    const newWindow = window.open(url, '_blank', 'noopener,noreferrer')
    if (newWindow)
      newWindow.opener = null
  }
  if (type == 'copy') {
    $('#cpy_link').val(data[1]);
    document.getElementById("cpy_link").style.display = "block";
    document.getElementById("cpy_link").select();
    document.execCommand("copy");
    document.getElementById("cpy_link").style.display = "none";
    window.show_notif('success', 'Mensaje copiado');
  }

} else {
  window.show_notif('error', data[1]);
}
});


});




});
</script>
@include('admin.blocks.cardScripts')
@endsection