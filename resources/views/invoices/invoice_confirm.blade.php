@extends('layouts.popup')
@section('content')
@include('invoices.invoice-content')
<?php printInvoiceContent($oInvoice,$items); ?>
<div class="row col-xs-12  text-center">
<button class="btn btn-complete" type="button" id="sendInvoiceEmail" data-id="{{$oInvoice->id}}" data-email="{{$oInvoice->email}}">Enviar</button>
<a class="btn btn-success" type="button" href="/admin/facturas/modal/editar/{{$oInvoice->charge_id}}" >Volver</a>
</div>
@endsection
@section('scripts')
@include('invoices.invoice-style')
<script>
$(function () {
  $('#sendInvoiceEmail').on('click',function (e){
    e.preventDefault();
    e.stopPropagation();
    if(confirm('Enviar factura a '+ $(this).data('email') +'?')){
      $('#loadigPage').show('slow');
       $.ajax({
        url: '/admin/facturas/enviar',
          type: 'POST',
          data: {
            id: $(this).data('id'),
            _token: "{{csrf_token()}}"
          }
        })
        .done(function () {
          window.show_notif('success', 'Factura enviada');
        })
        .fail(function () {
          window.show_notif('danger', 'Factura no enviada');
        })
        .always(function () {
          $('#loadigPage').hide('slow');
        });
      }
    });
  });
</script>
@endsection