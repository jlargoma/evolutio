@extends('layouts.admin-master')

@section('title') Liquidación horas extras - Evolutio HTS @endsection


@section('headerButtoms')
<li class="text-center">
  <a href="/admin/entrenadores/activos" class="btn btn-sm btn-success font-s16 font-w300">
    Volver
  </a>
</li>
@endsection

@section('content')
<div class="content  content-full bg-white">

  <div class="row mb-5">
    <div class="col-xs-12 btn-months ">
      @foreach($lstMonths as $k=>$v)
      <a href="<?php 
        if($k > date('m'))
          echo '#';
        else
          echo '/admin/horas-extras/list/?month=' . $k;
      ?>" class=" btn btn-success <?php echo ($month == $k) ? 'active' : '' ?>" 
      <?php echo ($k > date('m')) ? 'disabled' : '' ?> >
        {{$v.' '.$year}}
      </a>
      @endforeach
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      @include('horasExtras.table')
    </div>
  </div>
</div>


<div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Agregar Registro</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-8">
            <label>Descripción</label>
            <textarea class="form-control" id="descriptionAddItem" placeholder="Descripción" rows="1"></textarea>
          </div>
          <div class="col-xs-4">
            <label>Importe</label>
            <input class="form-control" id="amountAddItem" min="0" type="number" step="0.01" oninput="limitDecimals(this)" />
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 text-center">
            <input type="hidden" id="itemMonthAdd" value="{{$month}}" />
            <input type="hidden" id="itemUserAdd" value="" />
            <input type="hidden" id="itemDeptAdd" value="" />
            <button id="btnAddRegisterLq" class="btn btn-primary">Agregar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">

<script type="text/javascript">

  function limitDecimals(element) {
    let value = element.value;
    // Check if the input has more than two decimal places
    if (value.includes('.') && value.split('.')[1].length > 2) {
      // If more than two decimal places, truncate to two decimal places
      element.value = parseFloat(value).toFixed(2);
    }
  }

  $(document).ready(function() {
    $('#extra-hours-table-acc').on('click', '.d1', function(){
        var k = $(this).data('k');
        
        $('.d1_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            
            $('.d2_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d3_'+k).each(function(){
                  if ($(this).css('display') != 'none'){
                    var k = $(this).data('k');
                    $('.d4_'+k).hide();
                  }
                });
                $('.d3_'+k).hide();
              }
            });
            $('.d2_'+k).hide();
          }
        });
        
        $('.d1_'+k).toggle();
    });


    
    $('#extra-hours-table-acc').on('click', '.d2', function(){
        var k = $(this).data('k');
        
        $('.d2_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            $('.d3_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d4_'+k).hide();
              }
            });
            $('.d3_'+k).hide();
          }
        });
        
        $('.d2_'+k).toggle();
    });


    $('#extra-hours-table-acc').on('click', '.btn-delete-hours-item', function(){
      let $this = $(this);
      let dept = $this.data('dept');
      let user = $this.data('user');
      let id = $this.data('id');
      let amount = $('#item_' + id).data('amount');
      let userTotal = $('#user_'+user+'_total');
      let userTotalValue = userTotal.data('total');
      let roleTotal = $('#role_'+dept+'_total');
      let roleTotalValue = roleTotal.data('total');
      
      
      $.post( '/admin/horas-extras/delete', { 
        _token: '{{csrf_token()}}',
        id: id
      }).done(function (data) {
        if (data.status == 'OK') {

          userNewTotal = userTotalValue - amount;
          userTotal.data('total', userNewTotal)
          userTotal.text((''+userNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');

          roleNewTotal = roleTotalValue - amount;
          roleTotal.data('total', roleNewTotal)
          roleTotal.text((''+roleNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');
           
          $('#item_'+id).remove();
          
          window.show_notif('success', 'Registro eliminado.');

        } else {
          window.show_notif('error', 'No se pudo eliminar el registro.');
        }
        
      });
    });

    $('#extra-hours-table-acc').on('click', '.btn-edit-item', function(){
      let id = $(this).data('id');
      $('#action_btns_' + id + '_item').hide();
      $('#edit_btns_' + id + '_item').show();

      $('#item_' + id + '_amount').hide();
      $('#item_' + id + '_amount_edit').show();

      $('#item_' + id + '_description').hide();
      $('#item_' + id + '_description_edit').show();
    });



    $('#extra-hours-table-acc').on('click', '.btn-cancel-edit-item', function(){
      let id = $(this).data('id');
      $('#action_btns_' + id + '_item').show();
      $('#edit_btns_' + id + '_item').hide();

      $('#item_' + id + '_amount').show();
      $('#item_' + id + '_amount_edit').hide();
      
      $('#item_' + id + '_description').show();
      $('#item_' + id + '_description_edit').hide();
    });


    $('#extra-hours-table-acc').on('click', '.btn-approve-edit-item', function(){
      let $this = $(this);
      let dept = $this.data('dept');
      let user = $this.data('user');
      let id = $this.data('id');
      let amount = $('#item_' + id).data('amount');
      let userTotal = $('#user_'+user+'_total');
      let userTotalValue = userTotal.data('total');
      let roleTotal = $('#role_'+dept+'_total');
      let roleTotalValue = roleTotal.data('total');

      let newAmount = $('#item_' + id + '_amount_edit_input').val();
      let description = $('#item_' + id + '_description_edit_input').val();

      $.post( '/admin/horas-extras/edit', { 
        _token: '{{csrf_token()}}',
        id: id,
        amount: newAmount,
        description: description
      }).done(function (data) {
        if (data.status == 'OK') {

          userNewTotal = userTotalValue - amount + parseFloat(newAmount);
          userTotal.data('total', userNewTotal)
          userTotal.text((''+userNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');

          roleNewTotal = roleTotalValue - amount + parseFloat(newAmount);
          roleTotal.data('total', roleNewTotal)
          roleTotal.text((''+roleNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');
           
          $('#item_' + id + '_amount').text((''+parseFloat(newAmount).toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');
          $('#item_' + id + '_description').text(description);
          $('#item_' + id).data('amount',newAmount);

          $('#action_btns_' + id + '_item').show();
          $('#edit_btns_' + id + '_item').hide();

          $('#item_' + id + '_amount').show();
          $('#item_' + id + '_amount_edit').hide();
          
          $('#item_' + id + '_description').show();
          $('#item_' + id + '_description_edit').hide();
          
          window.show_notif('success', 'Registro actualizado.');

        } else {
          window.show_notif('error', 'No se pudo actualizar el registro.');
        }
      });
    });

    $('#extra-hours-table-acc').on('click', '.btn-add-item', function(){
      $('#itemUserAdd').val($(this).data('user'));
      $('#itemDeptAdd').val($(this).data('dept'));
      $('#modalAddNew').modal('show');
    });

    $('#btnAddRegisterLq').on('click', function () {
      let description = $('#descriptionAddItem').val();
      let amount = parseFloat($('#amountAddItem').val());
      let user = $('#itemUserAdd').val();
      let dept = $('#itemDeptAdd').val();
      let month = $('#itemMonthAdd').val();

      let userTotal = $('#user_'+user+'_total');
      let userTotalValue = parseFloat(userTotal.data('total'));
      let roleTotal = $('#role_'+dept+'_total');
      let roleTotalValue = parseFloat(roleTotal.data('total'));

      $.post( '/admin/horas-extras/add', { 
        _token: '{{csrf_token()}}',
        description: description,
        user: user,
        amount: amount,
        month: month
      }).done(function (data) {
        if (data.status == 'OK') {

          let userNewTotal = userTotalValue + amount;
          userTotal.data('total', userNewTotal)
          userTotal.text((''+userNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');

          let roleNewTotal = roleTotalValue + amount;
          roleTotal.data('total', roleNewTotal)
          roleTotal.text((''+roleNewTotal.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+' €');

          let htmlCode = '<tr id="item_' + data.details.id + '" data-amount="' + amount + '" class="d3 d2_' + dept + '_' + user + '" data-k="' + dept + '_' + user + '_' + data.details.id + '" style="display: table-row;">' +
                '<td class="static">' +
                  '<div id="item_' + data.details.id + '_description">' + description + '</div>' +
                  '<div style="display: none;" id="item_' + data.details.id + '_description_edit">' +
                    '<textarea id="item_' + data.details.id + '_description_edit_input" class="form-control" rows="1">' + description + '</textarea>' +
                  '</div>' +
                '</td>' +
                '<td>' +
                  '<b id="item_' + data.details.id + '_amount">' + (''+amount.toFixed(2)).replace(/\./g, ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ' €</b>' +
                  
                  '<div style="display: none;" id="item_' + data.details.id + '_amount_edit">' +
                    '<input style="max-width: 100px;margin:auto;" id="item_' + data.details.id + '_amount_edit_input" class="form-control" type="number" value="' + amount + '" step="0.01" oninput="limitDecimals(this)" min="0">' +
                  '</div>' +
                '</td>' +
                '<td>' +
                  '<div id="action_btns_' + data.details.id + '_item">' +
                    '<button ' + 
                      'data-id="' + data.details.id + '" ' +
                      'data-dept="' + dept + '" ' +
                      'data-user="' + user + '"  ' +
                      'class="btn btn-danger btn-xs btn-delete-hours-item"' +
                    '><i class="fa fa-times"></i></button> ' +
                    '<button ' +
                      'data-id="' + data.details.id + '" ' +
                      'class="btn btn-primary btn-xs btn-edit-item"' +
                    '><i class="fa fa-pencil"></i></button>' +
                  '</div>' +

                  '<div id="edit_btns_' + data.details.id + '_item" style="display: none;">' +
                    '<button ' +
                      'data-id="' + data.details.id + '" ' +
                      'class="btn btn-danger btn-xs btn-cancel-edit-item"' +
                    '><i class="fa fa-minus"></i></button> ' +

                    '<button ' +
                      'data-id="' + data.details.id + '" ' +
                      'data-dept="' + dept + '" ' +
                      'data-user="' + user + '"  ' +
                      'class="btn btn-success btn-xs btn-approve-edit-item"' +
                    '><i class="fa fa-check"></i></button>' +
                  '</div>'+
                  
                '</td>'+
              '</tr>';
          // Create a new element
          let newElement = $(htmlCode);

          // Get a reference to the target element
          let targetElement = $('#' + dept + '_' + user + '_add_row');
          
          // Insert the new element before the target element
          newElement.insertBefore(targetElement);

          $('#descriptionAddItem').val('');
          $('#amountAddItem').val('');
          
          $('#modalAddNew').modal('hide');
          window.show_notif('success', 'Registro agregado.');

        } else {
          window.show_notif('error', 'No se pudo agregar el registro.');
        }
        
      });
    });

  });
</script>
<style>
  .filtIncome {
    cursor: pointer;
  }

  .filtIncome.active {
    border: 1px solid #0046a0;
    background-color: #0067ea !important;
  }
</style>
@endsection