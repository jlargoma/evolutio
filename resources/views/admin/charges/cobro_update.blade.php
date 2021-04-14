@extends('layouts.popup')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<h2 class="text-center font-w300">
    ACTUALIZAR COBRO DE <b>{{$date}}</b> A <span class="font-w600"><?php echo strtoupper($user->name); ?></span>
</h2>
<h3 class="text-center font-w300 mt-1">
    Cuota a actualizar <span class="font-w600"><?php echo $rate->name; ?></span>
</h3>
<form class="fomr-horizontal content-md" method="post" action="{{ url('/admin/cobros/cobrar/' . $charge->id) }}" id="forms">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" id="importeOrig" value="<?php echo $rate->price; ?>">
    <input type="hidden" id="deleted" name="deleted" value="0">
    <p class="text-danger text-center font-w600 font-s32" ><?php echo $rate->price; ?>€</p>
    <input type="hidden" id="id_tax" name="id_tax" class="form-control" value="<?php echo $rate->id; ?>" />	 
    <div class="row">
        <div class="col-md-4">
            <label>Tipo</label><br/>
            <label class="css-input css-radio css-radio-lg css-radio-primary push-10-r">
                <input type="radio" name="type_payment" value="banco" @if ($charge->type_payment == "banco") checked="checked" @endif><span></span> Tarjeta
            </label>
            <label class="css-input css-radio css-radio-lg css-radio-primary">
                <input type="radio" name="type_payment" value="cash" @if ($charge->type_payment == "cash") checked="checked" @endif><span></span> Efectivo
            </label>
        </div>
        <div class="col-md-4">
            <label>Dto</label>
            <input type="number" id="discount" name="discount" class="form-control" value="{{$charge->discount}}" />
        </div>
        <div class="col-md-4">
            <label>Total</label>
            <input id="importeFinal" type="text" name="importe" class="form-control" value="{{ $charge->import}}" />
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6 col-xs-12 text-center">
            <button class="btn btn-lg btn-success" type="submit">
                Actualizar
            </button>
        </div>
        <div class="col-md-6 col-xs-12  text-center">
            <button class="btn btn-lg btn-danger" type="button" id="delete">
                Eliminar
            </button>
        </div>
    </div>
</div>
</form>
@endsection
@section('scripts')
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script type="text/javascript">
jQuery(function () {
    App.initHelpers(['datepicker', 'select2']);
});
$(document).ready(function () {
    $('#discount').change(function (event) {
        var discount = $(this).val();
        var importe = $('#importeOrig').val();
        var percent = discount / 100;

        $('#importeFinal').val(importe - (importe * percent));

    });
    $('#delete').on('click', function (event) {
        if (confirm('Eliminar cobro?')) {
            $('#deleted').val(1);
            $('#forms').submit();

        }

    });
});
</script>
@endsection