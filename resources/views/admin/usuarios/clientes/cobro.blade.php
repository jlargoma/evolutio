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
                    Cuota a cobrar <span class="font-w600"><?php echo $rate->name; ?></span> {{moneda($rate->price)}}
                </h2>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <label for="type_payment">Forma de pago</label>
                    <select class="form-control" name="type_payment" id="type_payment">
                            <option value="card">Tarjeta</option>
                            <option value="cash">Efectivo</option>
                            <option value="banco">Banco</option>
                    </select>
                    @include('admin.blocks.stripeBox')
                </div>
                <div class="col-xs-6">
                    <label for="discount">DTO %:</label>
                    <input type="number" id="discount" name="discount" class="form-control"/>
                    <div class="mt-2">
                        <label for="importeFinal">Total:</label>
                        <input id="importeFinal" type="text" name="importe" class="form-control"
                               value="<?php echo $rate->price; ?>"/>
                    </div>
                    <div class="mt-2 col-md-12 text-center">
                        <div class="col-xs-6">
                            <button class="btn btn-lg btn-success" type="submit" id="submitFormPayment">
                                Cobrar
                            </button>
                        </div>
                        <div class="col-xs-6">
                            <a class="btn btn-lg btn-danger"
                                href="{{ url('/admin/rates/unassigned')}}/<?php echo $user->id; ?>/<?php echo $rate->id; ?>/<?php echo $date; ?>">
                                Desasignar
                            </a>
                        </div>
                        <hr>
                        
                        <div class="col-xs-12">
                            <hr>
                            <h4 class=" my-1">Generar Link Pago Único Stripe</h4>
                        </div>
                        <div class="col-xs-6">
                            <label for="importeFinal">Email:</label>
                            <input id="u_email" type="text" class="form-control" value="<?php echo $user->email ?>"/>
                        </div>
                        <div class="col-xs-6">
                            <label for="importeFinal">Whatsapp:</label>
                            <input id="u_phone" type="text" class="form-control" value="<?php echo $user->telefono ?>"/>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-default btnStripe" data-t="mail">
                                <i class="fa fa-envelope"></i> Enviar Mail
                            </button>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-default btnStripe" data-t="wsp">
                                <i class="fa fa-whatsapp"></i> Enviar WSP 
                            </button>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-default btnStripe" data-t="copy">
                                <i class="fa fa-copy"></i> Copiar link Stripe
                            </button>
                        </div>
                        <textarea id="cpy_link" style="height: 0px; width: 0px; border: none; display: none;"></textarea>
                            
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
            <div style="clear: both;"></div>
            <div class="col-md-12 text-center push-20">
                
                <div class="col-md-4 col-xs-12">
                    
                </div>
                <div class="col-md-4 col-xs-12">
                    
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
    
     function detectMob() {
    const toMatch = [
        /Android/i,
        /webOS/i,
        /iPhone/i,
        /iPad/i,
        /iPod/i,
        /BlackBerry/i,
        /Windows Phone/i
    ];
//alert(navigator.userAgent);
    return toMatch.some((toMatchItem) => {
        return navigator.userAgent.match(toMatchItem);
    });
}
        
    $('.btnStripe').on('click', function(){
        var type = $(this).data('t');
        var posting = $.post( '/admin/send/cobro-mail', { 
                            _token: '{{csrf_token()}}',
                            id_rate: '{{$rate->id}}',
                            id_user: '{{$user->id}}',
                            date: '{{$year.'-'.$month.'-01'}}',
                            u_email: $('#u_email').val(),
                            u_phone: $('#u_phone').val(),
                            importe: $('#importeFinal').val(),
                            type: type
                            });
    	  	posting.done(function( data ) {
                    if (data[0] == 'OK'){
                        if (type == 'email'){
                            alert(data[1]);
                        }
                        if (type == 'wsp'){
                            if (detectMob()){
                                var url = 'whatsapp://send?text='+encodeURI(data[1]);
                            } else {
                                var url = 'https://web.whatsapp.com/send?phone='+$('#u_phone').val()+'&text='+encodeURI(data[1]);
                            }
                            const newWindow = window.open(url, '_blank', 'noopener,noreferrer')
                            if (newWindow) newWindow.opener = null
                        }
                        if (type == 'copy'){
                            $('#cpy_link').val(data[1]);
                            document.getElementById("cpy_link").style.display = "block";
                            document.getElementById("cpy_link").select();
                            document.execCommand("copy");
                            document.getElementById("cpy_link").style.display = "none"; 
                            alert('Mensaje copiado');
                        }
                        
                    }
    	 	});
                
                
    });
        
     
        
        
  });
</script>
@endsection