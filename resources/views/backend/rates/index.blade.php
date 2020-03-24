@extends('backend.base')

@section('content')

<div class="container-fluid">
  <div class="animated fadeIn">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="card">
          <div class="card-header">
            <i class="fa fa-align-justify"></i>{{ __('Tarifas') }}</div>
          <div class="card-body">
            @include('flash-message')
            <table class="table table-responsive-sm table-striped">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Precio</th>
                  <th>Coste</th>
                  <th>Nº Ses<span class="hidden-xs hidden-sm">ion / sem</span></th>
                  <th>Tipo</th>
                  <th>Tipo Pago</th>
                  <th>Tarifa</th>
                  <th>Borrar</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rates as $obj)
                <tr>
                  <td>
                    <input type="text" class="form-control editables name-rate-{{$obj->id}}"  data-id="{{$obj->id}}" value="{{$obj->name}}" />
                  </td>
                  <td>
                    <input type="number" class="form-control editables price-rate-{{$obj->id}}"  data-id="{{$obj->id}}" value="{{$obj->price}}" />
                  </td>
                  <td>
                    <input type="number" class="form-control editables cost-rate-{{$obj->id}}"  data-id="{{$obj->id}}" value="{{$obj->cost}}" />
                  </td>
                  <td>
                    <input type="number" class="form-control editables maxPax-rate-{{$obj->id}}"  data-id="{{$obj->id}}" value="{{$obj->max_pax}}" />
                  </td>
                  <td class="text-center ">
                    <select class="form-control editables type-rate-{{$obj->id}}" data-id="{{$obj->id}}">
                      <?php foreach ($services as $service): ?>
                        <option value="{{$service->id}}" <?php if ($service->id == $obj->type) echo "selected";?>>
                          {{$service->name}}
                        </option>
                      <?php endforeach ?>
                    </select>
                  </td>
                  <td class="text-center ">
                    <select class="form-control editables mode-{{$obj->id}}" data-id="{{$obj->id}}">
                      <option>----</option>
                      <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i ?>" <?php if ($i == $obj->mode) {
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
                  <td class="text-center ">
                    <select class="form-control editables code-rate-{{$obj->id}}" data-id="{{$obj->id}}">
                      <option value="">----</option>
                      @foreach ($lstCodes as $item)
                        <option value="{{$item}}" @if ($item == $obj->tarifa) selected @endif>
                          {{$item}}
                        </option>
                      @endforeach;
                    </select>
                  </td>
                  <td>
                    <form action="{{ route('tarifas.del', $obj->id ) }}" method="POST">
                      @method('DELETE')
                      @csrf
                      <button class="btn btn-block btn-danger" onclick="return confirm('Eliminar Tarifa?')">Delete</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')
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



      $.post("{{route('tarifas.upd')}}", 
        {_token: "{{csrf_token()}}",id: id, name: name, price: price, max_pax: max_pax, type: type, mode: mode, orden: orden,tarifa:tarifa,cost:cost},
        function (data) {
          if (data == 'ok'){
            alert('cambiada');
          } else {
            alert('error');
          }
      });
    });
  });
</script>
@endsection
