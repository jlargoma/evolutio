<!--    TABLA                                  -->
<table class="table">
  <thead>
    <tr>
      <th class='filtIncome active' data-k="0">Total<br>{{sumMonthValue($lstIncomesMonth)}}</th>
      @foreach($monts as $k=>$v)
      <th class='filtIncome' data-k="{{$k}}">{{$v}}<br />{{moneda($lstIncomesMonth[$k])}}</th>
      @endforeach
    </tr>
  </thead>
</table>

<div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th class="static thBlue">Servicio</th>
        <th class="first-col"></th>
        <th class="thBlue">Fecha</th>
        <th class="thBlue">Tipo</th>
        <th class="thBlue">Importe</th>
        <th class="thBlue">F. Pago</th>
        <th class="thBlue">Comentario</th>
        <th class="thBlue"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($lstIncomes as $i)
      <?php $m = intval(substr($i->date,5,2)); ?>
      <tr class="incomesMonths im_{{$m}}">
        <td class="static">{{$i->concept}}</td>
        <td class="first-col"></td>
        <td>{{convertDateToShow_text($i->date)}}</td>
        <td><?= isset($iLstRates[$i->type]) ? $iLstRates[$i->type] : ' - ' ?></td>
        <td><input style="max-width: 80px;" pattern="\d+(\.\d{1,2})?"  type="number" value="{{$i->import}}" step="0.01" id="importe{{$i->id}}"/></td>
        <td><?= ($i->type_payment) ? payMethod($i->type_payment) : ' - ' ?></td>
        <td>{{$i->comment}}</td>
        <td><button data-id="{{$i->id}}" class="btn btn-primary btn-actualizar">Actualizar</button></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>