<?php 
$auxTM = [];
$pmName = ['c'=>'Cash','v'=>'Visa','b'=>'Banco','i'=>'Inv. Evolutio','np'=>'No Pay'];
foreach ($lstMonths as $k=>$v){
  $auxTM[$k] = $pay_method['c'][$k] + $pay_method['v'][$k] + $pay_method['b'][$k] + $pay_method['np'][$k] + $pay_method['i'][$k];
}
?>
<br class='mt-1em' />
<h2 class="font-w600">
    Formas de cobros en <b><?php echo $year?></b>
</h2>
<div class="table-responsive" >
    <table class="table">
        <tr>
          <th class="static thBlue">Tipo</th>
          <th class="first-col"></th>
          <th class="">Total <br/>({{ moneda(array_sum($auxTM))}})</th>
          @foreach($lstMonths as $k=>$v)
          <th>{{$v}} <br/>({{ moneda($auxTM[$k])}})</th>
          @endforeach
        </tr>
         @foreach($pay_method as $k1=>$v1)
          <tr>
            <td class="static"><?php echo $pmName[$k1]; ?></td>
            <td class="first-col"></td>
            <td><b>{{ moneda(array_sum($v1))}}</b></td>
            @foreach($lstMonths as $k=>$v)
            <td>{{moneda($v1[$k])}}</td>
            @endforeach
          </tr>
            @endforeach
</table>
</div>

<?php 
if(isset($cashDepto)):
  $auxTM = [];
  $pmName = ['c'=>'Cash','v'=>'Visa','b'=>'Banco','np'=>'No Pay'];
  foreach ($lstMonths as $k=>$v){
    $auxTM[$k] = $cashDepto['esthetic'][$k] + $cashDepto['fisio'][$k] + $cashDepto['other'][$k];
  }
?>
<br class='mt-1em' />
<h2 class="font-w600">
    Cobros CASH por departamento en <b><?php echo $year?></b>
</h2>
<div class="table-responsive" >
    <table class="table">
        <tr>
          <th class="static thBlue">Tipo</th>
          <th class="first-col"></th>
          <th class="">Total <br/>({{ moneda(array_sum($auxTM))}})</th>
          @foreach($lstMonths as $k=>$v)
          <th>{{$v}} <br/>({{ moneda($auxTM[$k])}})</th>
          @endforeach
        </tr>
          <tr>
            <td class="static">Estética</td>
            <td class="first-col"></td>
            <td><b>{{ moneda(array_sum($cashDepto['esthetic']))}}</b></td>
            @foreach($lstMonths as $k=>$v)
            <td>{{moneda($cashDepto['esthetic'][$k])}}</td>
            @endforeach
          </tr>
          <tr>
            <td class="static">Fisio</td>
            <td class="first-col"></td>
            <td><b>{{ moneda(array_sum($cashDepto['fisio']))}}</b></td>
            @foreach($lstMonths as $k=>$v)
            <td>{{moneda($cashDepto['fisio'][$k])}}</td>
            @endforeach
          </tr>
          <tr>
            <td class="static">Otros</td>
            <td class="first-col"></td>
            <td><b>{{ moneda(array_sum($cashDepto['other']))}}</b></td>
            @foreach($lstMonths as $k=>$v)
            <td>{{moneda($cashDepto['other'][$k])}}</td>
            @endforeach
          </tr>
</table>
</div>
<?php 
endif;
?>