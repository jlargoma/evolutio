@extends('layouts.popup')
@section('content')
<style type="text/css">
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

    .stripe-price {
        background-color: white !important;
        padding: 8px 12px !important;
        border-radius: 4px !important;
        border: 1px solid transparent !important;
        box-shadow: 0 1px 3px 0 #e6ebf1 !important;
        -webkit-transition: box-shadow 150ms ease !important;
        transition: box-shadow 150ms ease !important;
    }
</style>
<div class="content" >
    <h2 class="text-center push-20"> ASIGNAR Y GENERAR COBRO PARA <?php echo strtoupper($user->name) ?></h2>
    <form class="form-toPayment" method="post" action="{{ url('/admin/cobros/cobrar-usuario') }}">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id_user" value="<?php echo $user->id; ?>">
        <div class="row">
            <div class="col-xs-12 col-md-6 push-20">
                <label for="id_rate">Tarifa</label>
                <select class="form-control" id="id_rate" name="id_rate" style="width: 100%; cursor: pointer"
                        placeholder="Seleccione tarifas.." required="">
                    <option></option>
                    <?php foreach ($rates as $rate): ?>
                        <?php if ($rate->status == 1): ?>
                            <?php $class = "green" ?>
                        <?php else: ?>
                            <?php $class = "blue" ?>

                        <?php endif ?>

                        <option value="<?php echo $rate->id ?>" data-price="<?php echo $rate->price ?>"
                                class="<?php echo $class; ?>">
                                    <?php echo $rate->name ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="col-xs-12 col-md-6 push-20">
                <label for="date_payment">Fecha de cobro</label>
                <input class="js-datepicker form-control" type="text" id="date_payment" name="date_payment"
                       data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y') ?>"
                       style="cursor: pointer;" required="">
            </div>

            <div class="col-xs-12 push-20">
                <div class="row push-20">
                    <div class="col-xs-6 push-20">
                        <label for="type_payment">Forma de pago</label>
                        <select class="form-control" name="type_payment" id="type_payment">
                            <option value="cash">Efectivo</option>
                            <option value="card">Banco</option>
                            <option value="banco">Tarjeta</option>
                        </select>
                    </div>

                    <div class="col-xs-6 push-20">
                        <label for="type_payment">% DTO:</label>
                        <input type="text" id="discount" name="discount"  class="form-control only-number"/>
                    </div>
                </div>
                <input type="hidden" id="importeCobrar">
            </div>
            <div class="col-xs-6 push-20">
                <label class="css-input css-radio css-radio-lg css-radio-primary push-10-r">
                    <input type="radio" name="operation" checked="" value="all"><span></span> Asignar & cobrar
                </label>
                <label class="css-input css-radio css-radio-lg css-radio-primary hidden">
                    <input type="radio" name="operation" value="charge"><span></span> Cobrar
                </label>
            </div>
            <div class="col-xs-6  push-20">
                <div class="col-md-3">
                    <h3>Total</h3>
                </div>
                <div class="col-md-9">
                    <input id="importeFinal" type="text" name="importe" class="form-control"/>
                </div>
            </div>
            <div class="col-md-12" id="content-payment" style="display: none;">
                <div class="row alert alert-info fade in alert-dismissable"
                     style="margin-top: 30px; background-color: #daeffd!important;">

                    <div class="row">
                        <div class="form-row col-xs-12 push-20">
                            <label for="card-element">
                                Datos de la tarjeta
                            </label>
                            <div id="card-element">
                                <!-- a Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display form errors -->
                            <div id="card-errors" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center push-20">
                <button class="btn btn-lg btn-success" type="submit" id="submitFormPayment">
                    Cobrar
                </button>
            </div>
        </div>
    </form>
</div>
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
    $('#id_rate').change(function (event) {
        var price = $("#id_rate option:selected").attr('data-price');
        $('#importeFinal').val(price);

    });

    $('#discount').change(function (event) {
        var discount = $(this).val();
        var importe = $('#importeFinal').val();
        var percent = discount / 100;

        $('#importeFinal').val(importe - (importe * percent));

    });

    $('.only-number').keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode >= 35 && e.keyCode <= 40)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#type_payment').change(function (e) {
        var value = $("#type_payment option:selected").text();
        console.log(value);

        if (value == "Tarjeta") {
            $('#content-payment').show();
            $('.form-toPayment').attr('id', 'paymentForm');
        } else {
            $('#content-payment').hide();
            $('.form-toPayment').removeAttr('id');
        }

    });
});
</script>
@endsection