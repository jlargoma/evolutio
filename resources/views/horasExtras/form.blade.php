<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.app')

@section('title') INFORME DE CONVENIOS - Evolutio HTS @endsection

@section('externalScripts')
<style>
  .bg-complete {
    color: #fff !important;
    background-color: #5c90d2 !important;
    border-bottom-color: #5c90d2 !important;
    font-weight: 800;
    vertical-align: middle !important;
  }

  option.b {
    font-weight: bold;
  }
</style>
@endsection
@section('content')
<div class="content content-boxed bg-gray-lighter">

  <h2 class="text-center">Liquidación mensual</h2>

  <div class="mb-5">
    <div>
      <div class="text-left">Nombre: <b>{{$user->name}}</b></div>
    </div>
    <div>
      <div class="text-left">Departamento: <b>{{$user->rol}}</b></div>
    </div>
  </div>

  <div class="row mb-5">
    <div class="col-xs-12 btn-months ">
      @foreach($lstMonths as $k=>$v)
      <a href="<?php 
        if($k > date('m'))
          echo '#';
        else
          echo '/horas-extras/' . $requestId . '/' . $token . '/' . $year . '-' . $k;
      ?>" class=" btn btn-success <?php echo ($month == $k) ? 'active' : '' ?>" 
      <?php echo ($k > date('m')) ? 'disabled' : '' ?> >
        {{$v.' '.$year}}
      </a>
      @endforeach
    </div>
  </div>




  <div class="row" id="content-table-inform">

    @if(count($items))
    <div class="table-responsive">
      <div class="col-md-12 col-xs-12 push-20">
        <table class="table table-striped table-header-bg">
          <tbody>
            <tr>
              <td class="text-center bg-complete font-w800" rowspan="2">RESUMEN</td>
              <td class="text-center bg-complete font-w800">SALARIO BASE</td>
              <td class="text-center bg-complete font-w800">TOTAL</td>
            </tr>
            <tr>
              <td class="text-center bg-complete">{{moneda($user->rates->salary, false, 2)}}</td>
              <td class="text-center bg-complete">{{moneda($total, false, 2)}} </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="table-responsive">
      <div class="col-md-12 col-xs-12">
        <table class="table table-striped table-header-bg">
          <thead>
            <tr>
              <th class="text-center">DESCRIPCIÓN</th>
              <th class="text-center">IMPORTE</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $data) : ?>
              <tr>
                <td class="text-center">{{$data->description}}</td>
                <td class="text-center">{{moneda($data->amount / 100, false, 2)}}</td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    @else

    @if ($errors->any())
      <div class="table-responsive">
        <div class="col-xs-12">
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        </div>
      </div>
    @endif

    <form action="{{url('/horas-extras/carga')}}" method="post" enctype="multipart/form-data">

      <input type="hidden" name="requestId" value="{{$requestId}}">
      <input type="hidden" name="month" value="{{$month}}">
      <input type="hidden" name="year" value="{{$year}}">
      <input type="hidden" name="token" value="{{$token}}">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" id="userSalary" name="userSalary" value="<?php echo $user->rates->salary; ?>">

      <div class="table-responsive">
        <div class="col-md-12 col-xs-12">
          <h3>Carga de horas extras {{$lstMonths[(int)$month]}} {{$year}}</h3>
          <small class="mb-5"><b>Importante:</b> No envie el formulario sin completar todas las horas extras con su respectiva descripción. Una vez enviado solo el administrador podra editar la información enviada.</small>
        </div>
      </div>

      <div class="table-responsive">
        <div class="col-md-12 col-xs-12">
          <table class="table table-striped table-header-bg table-form-liquidation">
            <thead>
              <tr>
                <th class="text-center">DESCRIPCIÓN</th>
                <th class="text-center">IMPORTE</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="formInputDynamic">
              <tr>
                <td class="text-center">Salario base</td>
                <td class="text-center">{{moneda($user->rates->salary, false, 2)}}</td>
                <td></td>
              </tr>
              <tr>
                <td class="text-center">
                  <textarea class="form-control" name="description[]" rows="1"></textarea>
                </td>
                <td class="text-center">
                  <input class="form-control" name="amount[]" type="number" value="" min="0" step="0.01" oninput="limitDecimalsAndRecalculate(this)" />
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="col-xs-12">
        <button class="btn btn-primary pull-right" id="addExtraHours" type="button">Agregar item</button>
      </div>

      <div class="col-xs-12 text-right">
        <h3>Total mes: <b id="totalMes">{{$user->rates->salary}}</b></h3>
      </div>

      <div class="col-xs-12">
        <button class="btn btn-success btn-lg" type="submit">ENVIAR</button>
      </div>
    </form>
    @endif

  </div>


</div>

@endsection
@section('scripts')
<style>
  .mx-1em {
    margin-top: 1em;
    margin-bottom: 1em;
  }

  a.btn.btn-success {
    margin: 7px 2px;
  }


  table.table-form-liquidation {
    width: 100%;
    /* Set table width to 100% */
    border-collapse: collapse;
    /* Collapse table borders */
  }

  table.table-form-liquidation td:first-child {
    width: 70%;
    /* Set width of the first column to 70% */
  }

  table.table-form-liquidation td:second-child {
    width: 20%;
    /* Set width of the second column to 30% */
  }

  table.table-form-liquidation td:last-child {
    width: 10%;
    /* Set width of the second column to 30% */
  }

  /* Optional: Add some styling for demonstration */
  table.table-form-liquidation td {
    border: 1px solid #ddd;
    /* Add borders to table cells */
    padding: 8px;
    /* Add padding to table cells */
  }
</style>
<script type="text/javascript">

  function recalculateTotal(){
    let salary = parseFloat($('#userSalary').val());
    let totalInputs = 0;
    // Iterate over all number inputs with name "colors[]"
    $('input[name="amount[]"]').each(function() {
      totalInputs += parseFloat($(this).val()); // Add value to the total
    });

    $('#totalMes').text(salary + totalInputs);
  }

  function limitDecimalsAndRecalculate(element) {
    let value = element.value;
    // Check if the input has more than two decimal places
    if (value.includes('.') && value.split('.')[1].length > 2) {
      // If more than two decimal places, truncate to two decimal places
      element.value = parseFloat(value).toFixed(2);
    }

    recalculateTotal();
  }

  $(document).ready(function() {
    $("#addExtraHours").click(function() {
      $("#formInputDynamic").append('<tr>' +
        '<td class="text-center">' +
        '<textarea class="form-control" name="description[]" rows="1"></textarea>' +
        '</td>' +
        '<td class="text-center">' +
        '<input class="form-control" name="amount[]" type="number" value="" min="0" step="0.01" oninput="limitDecimalsAndRecalculate(this)" />' +
        '</td>' +
        '<td><button class="btn btn-danger btn-delete-item btn-xs" type="button">&times;</button></td>' +
        '</tr>');
    });

    $('#formInputDynamic').on('click', '.btn-delete-item', function() {
      $(this).closest("tr").remove();
      recalculateTotal();
    });
  });
</script>
@endsection