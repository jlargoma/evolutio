<!--    TABLA                                  -->
<table class="table">
  <thead>
    <tr>
      <th class="">Total<br>{{sumMonthValue($lstIncomesMonth)}}</th>
      @foreach($monts as $k=>$v)
      <th class='filtIncome' date-k="{{$k}}">{{$v}}<br />{{moneda($lstIncomesMonth[$k])}}</th>
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
        <th class="thBlue">Comentario</th>

      </tr>
    </thead>
    <tbody>
      @foreach($lstIncomes as $i)
      <?php $m = intval(substr($i->date,5,2)); ?>
      <tr class="incomesMonths" data-k="{{$m}}">
        <td class="static">{{$i->concept}}</td>
        <td class="first-col"></td>
        <td>{{convertDateToShow_text($i->date)}}</td>
        <td><?= isset($iLstRates[$i->type]) ? $iLstRates[$i->type] : ' - ' ?></td>
        <td>{{moneda($i->import)}}</td>
        <td>{{$i->comment}}</td>
        <td></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>