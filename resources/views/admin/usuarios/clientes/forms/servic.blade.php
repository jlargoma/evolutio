<h3 class="text-left">SERVICIOS ASOCIADOS</h3>
<div class="table-responsive">
  <table class="table">
    <tr>
      <th>Servicio</th>
      <th>Entrenador</th>
      <th>Precio</th>
      <th></th>
    </tr>
    @if($subscrLst)
    @foreach($subscrLst as $r)
    <?php 
    if (!isset($aRates[$r->id_rate])){ continue;}
    $aux =  $aRates[$r->id_rate] ; 
    $coach = isset($aCoachs[$r->id_coach]) ? $aCoachs[$r->id_coach] : '--';
    ?>
    <tr>
      <td>{{$aux->name}}
      <?php if($r->tarifa == 'fidelity') echo '<i class="fa fa-heart text-success"></i>'; ?>
      </td>
      <td>{{$coach}}</td>
      <td><input type="number" step="0.01" data-r="{{$r->id}}" value="{{$r->price}}" class="subscr_price">€</td>
      <td>
        <a 
          href="/admin/clientes-unsubscr/{{ $user->id }}/{{$r->id}}"
          onclick="return confirm('Remover el servicio para el periodo en curso?')"
          >
          <i class="fa fa-trash "></i>
        </a>
      </td>
    </tr>
    @endforeach
    @endif
  </table>
</div>
<br/><hr/><br/>
<div class="row my-1">
  <form action="/admin/add-subscr" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $user->id }}">
    <div class="col-md-4  push-20">
      <div class="form-material">
        <select class="form-control" id="id_rateSubscr" name="id_rate" style="width: 100%; cursor: pointer" data-placeholder="Seleccione tarifas.." >
          <option></option>
          <?php foreach ($subscrRates as $rate): 
              $price = $rate->price;
              $tarifa = '';
              if ($rate->tarifa == 'fidelity'){
                if ($uFidelity == 0){
                  $price = priceNoFidelity($price);
                  $tarifa = 'nofidelity';
                }
                if ($uFidelity == 1) $tarifa = 'fidelity';
              }
  
            ?>
            <option value="{{$rate->id}}" data-t="{{$rate->type}}" data-p="{{$price}}" data-tarifa="{{$tarifa}}">
              <?php echo $rate->name ?>
            </option>
          <?php endforeach ?>
        </select>
        <label for="id_rate">Agregar Servicio</label>
      </div>
    </div>
    <div class="col-md-3  push-20">
      <div class="form-material">
        <select class="form-control" name="id_rateCoach" id="id_rateCoach" disabled>
          <option value=""> -- </option>
          <?php
          foreach ($aCoachs as $k => $v) {
            echo '<option value="' . $k . '">' . $v . '</option>';
          }
          ?>
        </select>
        <label for="id_rate">Entrenador</label>
      </div>
    </div>
    <div class="col-md-2  push-20">
      <div class="form-material">
        <input class="form-control" type="number" id="r_price" name="r_price" step="0.01" required value="">
        <label for="price">Precio</label>
      </div>
    </div>
    <div class="text-center col-md-1 push-20" id="showTartifa"></div>
    <div class="col-md-2  push-20">
      <button class="btn btn-success">Agregar</button>
    </div>
  </form>
</div>