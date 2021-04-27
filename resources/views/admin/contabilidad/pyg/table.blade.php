<!--    TABLA                                  -->

<h2 class="font-w600">
    Listado de Ingresos / Egresos de <b><?php echo $year?></b>
</h2>
<div class="table-responsive" >
    <table class="table">
      <thead>
        <tr>
          <th class="static">Ingresos</th>
          <th class="first-col"></th>
          <th class="">Total</th>
          @foreach($monts as $k=>$v)
          <th>{{$v}}</th>
          @endforeach
        </tr>
       </thead>
       <tbody>
         @foreach($crLst as $k1=>$v1)
          <tr>
            <td class="static">{{$oRateTypes[$k1]}}</td>
            <td class="first-col"></td>
            <?php 
            $sumMonthValue = sumMonthValue($v1);
            $aux_i[0]+= $sumMonthValue;
            ?>
            <td><b>{{ moneda($sumMonthValue)}}</b></td>
            @foreach($monts as $k=>$v)
            <?php $aux_i[$k]+= $v1[$k];?>
            <td>{{moneda($v1[$k])}}</td>
            @endforeach
          </tr>
            @endforeach
          <tr class="subtotal">
            <td class="static">Subtotal</td>
            <td class="first-col"></td>
            @foreach($aux_i as $v)
            <td>{{moneda($v)}}</td>
            @endforeach
          </tr>
       </tbody>
       <thead>
        <tr>
          <th class="static">Egresos</th>
          <th class="first-col"></th>
          <th class="">Total</th>
          @foreach($monts as $k=>$v)
          <th>{{$v}}</th>
          @endforeach
        </tr>
       </thead>
       <tbody>
          @foreach($ggMonth as $k1=>$v1)
          <tr>
            <td class="static">{{$ggNames[$k1]}}</td>
            <td class="first-col"></td>
            <?php 
            $sumMonthValue = sumMonthValue($v1);
            $aux_e[0]+= $sumMonthValue;
            ?>
            <td><b>{{moneda($sumMonthValue)}}</b></td>
            @foreach($monts as $k=>$v)
            <?php $aux_e[$k]+= $v1[$k];?>
            <td>{{moneda($v1[$k])}}</td>
            @endforeach
          </tr>
            @endforeach
          <tr class="subtotal">
            <td class="static">Subtotal</td>
            <td class="first-col"></td>
            @foreach($aux_e as $v)
            <td>{{moneda($v)}}</td>
            @endforeach
          </tr>
       </tbody>
       <tfoot>
         <tr class="">
            <td class="static">RESULTADO</td>
            <td class="first-col"></td>
            @foreach($aux_e as $k=>$v)
            <td>{{moneda($aux_i[$k]-$v)}}</td>
            @endforeach
          </tr>
       </tfoot>
       
</table>
</div>
<!--    TABLA                                  -->