@extends('layouts.admin-master')

@section('title') Servicios - Evolutio HTS @endsection

@section('headerButtoms')
<li class="text-center">
  <button class="btn btn-sm btn-success new-rate" data-toggle="modal" data-target="#modal-rate">
    <i class="fa fa-plus"></i> Servicio
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
      <div class="block-content">
        <?php if (count($newRates) > 0): ?>
          <table class="table table-bordered table-striped js-dataTable-full table-header-bg">
            <thead>
              <tr>
                <th class="text-center hidden-xs hidden-sm" style="background-color: #46c37b; width: 60px;">id</th>
                <th class="text-center" style="background-color: #46c37b; min-width: 280px;">Nombre</th>
                <th class="text-center" style="background-color: #46c37b; width: 90px;">Precio</th>
                <th class="text-center" style="background-color: #46c37b; width: 90px;">Nº Ses<span class="hidden-xs hidden-sm">ion / sem</span></th>
                <th class="text-center" style="background-color: #46c37b">Tipo</th>
                <th class="text-center" style="background-color: #46c37b">Tipo Pago</th>
                <th class="text-center" style="background-color: #46c37b;min-width: 10%;">Acciones</th>
              </tr>
            </thead>
            <tbody>	
              <?php $lstCodes = rates_codes(); ?>
              <?php foreach ($newRates as $rate): ?>
                <tr>
                  <td class="text-center hidden-xs hidden-sm"><?php echo $rate->id ?></td>
                  <td class="text-center">
                    <input type="text" class="form-control editables name-rate-<?php echo $rate->id ?>"  data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->name; ?>" />
                  </td>
                  <td class="text-center">
                    <input type="text"  class="form-control editables price-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->price; ?>" />
                  </td>
                  <td class="text-center ">
                    <input type="text" class="form-control editables maxPax-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>" value="<?php echo $rate->max_pax; ?>" />
                  </td>
                  <td class="text-center ">
                    <select class="form-control editables type-rate-<?php echo $rate->id ?>" data-id="<?php echo $rate->id; ?>">
                      <?php foreach ($types as $t): ?>
                        <option value="<?php echo $t->id ?>" <?php if ($t->id == $rate->type) {
                            echo "selected";
                          } ?>>
                        <?php echo $t->name ?>
                        </option>
                    <?php endforeach ?>
                    </select>
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
                      <?php foreach ($types as $t): ?>
                        <?php if ($t->id == $rate->type) echo $t->name ?>
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

      var name = $('.name-rate-' + id).val();
      var price = $('.price-rate-' + id).val();
      var max_pax = $('.maxPax-rate-' + id).val();
      var type = $('.type-rate-' + id).val();
      var mode = $('.mode-' + id).val();
      var orden = $('.orden-' + id).val();
      var tarifa = $('.code-rate-' + id).val();
      var cost = $('.cost-rate-' + id).val();

      $.get('/admin/tarifas/update/', 
        {id: id, name: name, price: price, max_pax: max_pax, type: type, mode: mode, orden: orden,tarifa:tarifa,cost:cost},
        function (data) {
//        alert(data);
      });
    });
  });
</script>
@endsection