@extends('layouts.popup')
@section('content')
<div class="content" style="max-width:975px;">
  <div class="col-xs-12 not-padding push-20">
    <h2 class="text-center font-w300">COMPRA DE BONOS</h2>
  </div>
  <form class="form-toPayment" method="post" action="{{ url('/admin/bonos/comprar') }}">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id_user" value="<?php echo $user->id; ?>">
    <input type="hidden" name="type" value="<?php echo $type; ?>">
    <input type="hidden" name="id_back" value="<?php echo $id_back; ?>">
    <div class="row">
      @if($oBonos)
        @foreach($oBonos as $b)
        <div class="col-md-6 col-xs-12">
          <div class="checkBono">
          <input type="radio" name="id_bono" value="{{$b->id}}" class="form-control"/>
          <label>{{$b->name}}</label>
          <price>{{moneda($b->price)}}</price>
          <span>
            <b>{{$b->quantity}}</b> Bonos de <b>{{moneda($b->value)}}</b> C/U
          </span>
          </div>
        </div>
        @endforeach
      @endif
      <div class="col-xs-6">
        <div class="box-payment-card row">
          <h4>PAGAR AHORA</h4>
          <div class="col-xs-12">
            <div class="col-xs-9">
              <select class="likeBtn" name="type_payment" id="type_payment" multiple>
                <option value="card" selected="">Tarjeta</option>
                <option value="cash">Efectivo</option>
                <option value="banco">Banco</option>
              </select>
            </div>
            <div class="col-xs-3">
              <button class="btn btn-lg btn-success" type="submit" id="submitFormPayment">
                Cobrar
              </button>
            </div>
            <div class="col-xs-12">
              @include('admin.blocks.stripeBox')
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-6 col-md-6">
        
      </div>
    </div>
  </form>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
$(document).ready(function () {
  
  $('input[type=radio][name=id_bono]').on('change', function() {
    var val = $(this).val();
    $('.checkBono').removeClass('active');
    $(this).closest('.checkBono').addClass('active');
  });



  $('#type_payment').change(function (e) {
    var value = $("#type_payment option:selected").val();
    if (value == "card") {
      $('#stripeBox').find('.disabled').show();
      $('.form-toPayment').attr('id', 'paymentForm');
    } else {
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
      u_bono: 1,
      u_email: $('#u_email').val(),
      u_phone: $('#u_phone').val(),
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