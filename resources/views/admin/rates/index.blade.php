@extends('layouts.admin-master')

@section('title') Servicios - Evolutio HTS @endsection

@section('headerButtoms')
<li class="text-center">
  <button class="btn btn-sm btn-success new-rate" data-toggle="modal" data-target="#modal-rate">
    <i class="fa fa-plus"></i> Servicio
  </button>
</li>
<li class="text-center">
  <button class="btn btn-sm btn-success openFloatBoxRates">
    <i class="fa fa-pencil"></i> Servicio
  </button>
</li>
@endsection

@section('content')
<div class="content content-full bg-white">
  <div class="row" style="padding: 20px 0;">
    <div class="col-xs-12 col-md-12">
    <div class="row">
      <h3 class="text-center">
        Listado de Servicios
      </h3>
      <div class="table-responsive">
        <?php if (count($services) > 0): ?>
          <table class="table table-bordered table-striped js-dataTable-full table-header-bg">
            <thead>
              <tr>
                <th class="text-center hidden-xs hidden-sm">id</th>
                <th class="text-center">Familia</th>
                <th class="text-center">SUBFAMILIA</th>
                <th class="text-center">Servicio</th>
                <th class="text-center">Plan</th>
                <th class="text-center">Show</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Nº Ses<span class="hidden-xs hidden-sm">ion / sem</span></th>
                <th class="text-center">Periodicidad</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>	
              <?php foreach ($services as $tRate=>$serv): ?>
              <?php foreach ($serv as $rate): ?>
                <tr>
                  <td class="text-center hidden-xs hidden-sm"><?php echo $rate->id ?></td>
                  <td class="text-center td1">
                    <select class="form-control editables type-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>">
                      <?php foreach ($types as $k=>$v): ?>
                        <option value="<?php echo $k ?>" <?php if ($k == $rate->type) {
                            echo "selected";
                          } ?>>
                        <?php echo $v ?>
                        </option>
                    <?php endforeach ?>
                    </select>
                  </td>
                  <td class="text-center ">
                    <select class="form-control editables subfamily-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>">
                      <option value="">---</option>
                      <?php foreach ($subfamily as $k=>$v): ?>
                        <option value="<?php echo $k ?>" <?php if ($k == $rate->subfamily) {
                            echo "selected";
                          } ?>>
                        <?php echo $v ?>
                        </option>
                    <?php endforeach ?>
                    </select>
                  </td>
                  <td class="text-center td1">
                    <input type="text" class="form-control editables name-rate-<?php echo $rate->id ?>"  data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->name; ?>" />
                  </td>
                  <td class="text-center">
                    <span style="font-size: 23px;" class="rate_fidelity" data-k="<?php echo $rate->id ?>" data-v="<?php echo $rate->tarifa ?>">
                      <i class="fa fa-heart text-success fidelity" <?php echo ($rate->tarifa != 'fidelity') ? 'style="display:none;"' : ''; ?>></i>
                      <i class="fa fa-heart-o no_fidelity" <?php echo ($rate->tarifa == 'fidelity') ? 'style="display:none;"' : ''; ?>></i>
                    </span>
                  </td>
                  <td class="text-center">
                    <span style="font-size: 23px;" class="rate_showLst" data-k="<?php echo $rate->id ?>" data-v="<?php echo $rate->show_list ?>">
                      <i class="fa fa-eye text-success showLst_1" <?php echo ($rate->show_list == 0) ? 'style="display:none;"' : ''; ?>></i>
                      <i class="fa fa-eye-slash showLst_0" <?php echo ($rate->show_list == 1) ? 'style="display:none;"' : ''; ?>></i>
                    </span>
                  </td>
                  <td class="text-center">
                    <input type="text"  class="form-control editables price-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->price; ?>" />
                  </td>
                  <td class="text-center ">
                    <input type="text" class="form-control editables maxPax-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->max_pax; ?>" />
                  </td>
                  
                  <td class="text-center ">
                    <select class="form-control editables mode-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>">
                      <option>----</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i ?>" <?php if ($i == $rate->mode) {
                      echo "selected";
                    } ?>>
                          <?php echo $i ?>
                          <?php if ($i > 2): ?>
                            Meses
                        <?php else: ?>
                            Mes
                        <?php endif ?>
                        </option>

                      <?php endfor; ?>
                    </select>
                  </td>
                
                  <td class="text-center">
                    <div class="btn-group">
                      <!--  -->
                      <a href="{{ url('/admin/tarifas/delete/')}}/<?php echo $rate->id ?>" class="btn btn-md btn-danger" type="button" data-toggle="tooltip" title="" data-original-title="Eliminar Tarifa" onclick="return confirm('Are you sure you want to delete this item?');">
                        <i class="fa fa-times"></i>
                      </a>
                    </div>
                  </td>
                </tr>
  <?php endforeach ?>
  <?php endforeach ?>
            </tbody>
          </table>
<?php else: ?>
          <div class="col-xs-12">
            <h2 class="text-muted font-w200">
              No hay <span class="font-w600">Servicios</span> creada <span class="font-w600"></span>, por favor cree una nueva aquí
            </h2>
          </div>
      <?php endif ?>
      </div>
    </div>

    <div class="row">
       
      <h3 class="text-center">
        Listado de Servicios Antiguos <i class="fa fa-eye text-primary showOldRates" style="cursor: pointer;"></i>
      </h3>
      <div class="block-content oldRatesContent" style="display: none;">
<?php if (count($oldRates) > 0): ?>
          <table class="table table-bordered table-striped js-dataTable-full table-header-bg">
            <thead>
              <tr>
                <th class="text-center hidden-xs hidden-sm" style=" width: 60px;">id</th>
                <th class="text-left">Nombre</th>
                <th class="text-center">Precio</th>
                <th class="text-center">Nº Ses<span class="hidden-xs hidden-sm">ion / sem</span></th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Tipo Pago</th>
              </tr>
            </thead>
            <tbody>	
  <?php foreach ($oldRates as $rate): ?>
                <tr>
                  <td class="text-center hidden-xs hidden-sm">
                    <?php echo $rate->order ?>
                  </td>
                  <td class="text-left">
                    <?php echo ($rate->name) ?>
                  </td>
                  <td class="text-center">
                    <?php echo $rate->price; ?>
                  </td>
                  <td class="text-center ">
                    <?php echo $rate->max_pax; ?>
                  </td>
                  <td class="text-center ">
                      <?php foreach ($types as $k=>$v): ?>
                        <?php if ($k == $rate->type) echo $v ?>
                      <?php endforeach ?>
                  </td>
                  <td class="text-center ">
                        <?php echo $rate->mode." Meses"; ?>
                  </td>
                
                </tr>		
  <?php endforeach ?>	             
            </tbody>
          </table>
<?php else: ?>
          <div class="col-xs-12">
            <h2 class="text-muted font-w200">
              No hay <span class="font-w600">Servicios</span> creada <span class="font-w600"></span>, por favor cree una nueva 
               <b class="new-rate" data-toggle="modal" data-target="#modal-rate">aquí</b>
            </h2>
          </div>
<?php endif ?>
      </div>
    </div>

  </div> 
</div>
</div>
</div>
<div class="modal fade" id="modal-rate" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
            </li>
          </ul>
        </div>
        <div class="row block-content" id="content-rate">

        </div>
      </div>
    </div>
  </div>
</div>

<div class="floatBox" id="floatBoxRates" style="display: none;">
  <div class="header">Filtros de Home</div>
  <div class="body">
    <div class="table-responsive">
      <table class="table table-bordered table-header-bg">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>IDs</th>
            <th>Icono</th>
            <th>X</th>
          </tr>
        </thead>
        <tbody class="listFields">

        </tbody>
      </table>
    </div>
    <div ></div>
  </div>
  <div class="footer">
    <button role="button" class="btn btn-success addFloatBoxRates">Agregar</button>
    <button role="button" class="btn btn-success saveFloatBoxRates">Guardar</button>
    <button role="button" class="btn btn-default closeFloatBoxRates">Cerrar</button>

    <a href="https://fontawesome.com/v4/icons/" target="_blank">Listado de iconos</a>
  </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {

    $('.new-rate').click(function (event) {
      $.get('/admin/tarifas/new', function (data) {
        $('#content-rate').empty().append(data);
      });
    });

    $('.showOldRates').click(function () {
      $('.oldRatesContent').toggle();
    })


    $('.editables').change(function (event) {
            
        var id = $(this).attr('data-id');
        var data= {
            id: id,
            name: $('.name-rate-' + id).val(),
            price: $('.price-rate-' + id).val(),
            max_pax: $('.maxPax-rate-' + id).val(),
            type: $('.type-rate-' + id).val(),
            mode: $('.mode-' + id).val(),
            cost:$('.cost-rate-' + id).val(),
            subfamily:$('.subfamily-' + id).val(),
        };
        
      $.get('/admin/tarifas/update/', data);
    });
    
    
    
    $('.rate_fidelity').on('click',function(){
      var that = $(this);
      var val = that.data('v');
      if (val == '') val = 'no_fidelity';
      var newVal = 'fidelity';
      if (val == 'fidelity') newVal = 'no_fidelity';

      var data= {
            id: that.data('k'),
            val: newVal,
            _token: '{{csrf_token()}}'
        };
        
      $.post('/admin/tarifas/upd_fidelity', data).done(
        function (resp) {
          if (resp == 'OK'){
             window.show_notif('success', 'tipo de plan actualizado');
             that.find('.'+newVal).show();
             that.find('.'+val).hide();
             that.data('v',newVal);
          } else {
             window.show_notif('error', 'tipo de plan NO actualizado');
          }
        });
      
    });
    
    $('.rate_showLst').on('click',function(){
      var that = $(this);
      var val = parseInt(that.data('v'));
      var newVal = 1;
      if (val) newVal = 0;
      var data= {
            id: that.data('k'),
            val: newVal,
            _token: '{{csrf_token()}}'
        };
        
      $.post('/admin/tarifas/upd_showLst', data).done(
        function (resp) {
          if (resp == 'OK'){
             window.show_notif('success', 'Actulizado');
             console.log('.showLst_'+newVal);
             that.find('.showLst_'+newVal).show();
             that.find('.showLst_'+val).hide();
             that.data('v',newVal);
          } else {
             window.show_notif('error', 'NO Actulizado');
          }
        });
      
    });
    

    /** 
     * 
     */
    var addFloatBoxRates = function(item){
      var field = '<tr>'+
      '<td><input name="names[]" value="'+item.name+'"></td>'+
      '<td><input name="ids[]" value="'+item.ids+'"></td>'+
      '<td><input name="icons[]" value="'+item.icon+'"></td>'+
      '<td><button role="button" class="btn btn-danger removeFloatBoxRates">X</button></td>'+
      '</tr>';
      $('.listFields').append(field);
    };
    var listFloatBoxRates = JSON.parse('<?= ($customFamilyRates) ? $customFamilyRates : "{}"; ?>');
    for(i in listFloatBoxRates){
      addFloatBoxRates(listFloatBoxRates[i]);
    }

    $('.addFloatBoxRates').on('click',function(){
      addFloatBoxRates({name:'',ids:'',icon:''});
    });
    $('.saveFloatBoxRates').on('click',function(){
      var items = $('.listFields');
      var data = {
        'names': items.find('input[name^="names"]' ).map(function(){return $(this).val();}).get(),
        'ids': items.find('input[name^="ids"]' ).map(function(){return $(this).val();}).get(),
        'icons': items.find('input[name^="icons"]' ).map(function(){return $(this).val();}).get(),
        _token: '{{csrf_token()}}'
      }


      $.post('/admin/saveCustomFamilyRates', data).done(
        function (resp) {
          if (resp == 'OK'){
            window.show_notif('success', 'Actualizado');
          } else {
            window.show_notif('error', 'NO Actualizado');
          }
          $('#floatBoxRates').removeClass('show');
        });
    });
    $('.closeFloatBoxRates').on('click',function(){
      $('#floatBoxRates').removeClass('show');
    });
    $('.openFloatBoxRates').on('click',function(){
      $('#floatBoxRates').addClass('show');
    });

    $('.listFields').on('click','.removeFloatBoxRates',function(){
      $(this).closest('tr').remove();
    });

  

  });
</script>
<style>
.td1 {
    min-width: 11em;
    padding: 16px 1px 0 !important;
}
.floatBox {
  position: fixed;
  right: 2em;
  max-width: 80%;
  min-height: 9em;
  top: 5em;
  background-color: #FFF;
  bottom: 3px;
  padding: 12px;
  box-shadow: 1px 1px 6px 2px #c3c3c3;
}
.floatBox.show{
  display: block;
}
.floatBox .header {
    font-size: 2em;
    border-bottom: 2px solid #C3C3C3;
    margin-bottom: 1em;
}
.floatBox th,.floatBox td {
    padding: 7px !important;
    text-align: center;
}
</style>
@endsection