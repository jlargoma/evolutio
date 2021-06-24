<?php $result = []; ?>
<div class="table-responsive">
    <table class="table table-bordered table-liq table-header-bg">
        <thead>
            <tr>
              <th class="text-center static" style="height: 66px;">{{$year}}</th>
                <th class="first-col"></th>
                @foreach($aMonths as $k=>$v)
                <th class="text-center">{{$v}}</th>
                @endforeach
                <th class="text-center">Total</th>
                
            </tr>
        </thead>
        <tbody>
            <!-- LIQUIDACIONES -->
            <tr>
              <td class="text-center static" style="height: 56px;">
                    <b>PAGOS MENS.</b>
                </td>
                <td class="first-col"></td>
                @foreach($aMonths as $k=>$v)
                <?php 
                  $aux = (isset($liqLst[$k])) ? $liqLst[$k] : 0;
                  $result[$k] = $aux;
                ?>
                <td class="text-center">
                    <input type="number" 
                           data-k="{{$k}}" 
                           class="form-control liquidation" 
                           value="{{$aux}}">
                </td>
                @endforeach
                <td class="text-center">{{moneda($anual)}}</td>
            </tr>
            <tr>
                <td class="text-center static">
                    <b>PAGADO</b>
                </td>
                <td class="first-col"></td>
                @foreach($aMonths as $k=>$v)
                <?php 
                  $aux = (isset($payMonth[$k])) ? $payMonth[$k] : 0;
                  $result[$k] += $aux;
                ?>
                <td class="text-center">
                  {{moneda($aux)}}
                </td>
                @endforeach
                <td class="text-center">{{moneda(array_sum($payMonth))}}</td>
            </tr>
        </tbody>
        <tfoot>
          <tr>
            <td class="static">Total</td>
            <td class="first-col"></td>
                @foreach($aMonths as $k=>$v)
                <td>
                  {{moneda($result[$k])}}
                </td>
                @endforeach
                <td>{{moneda(array_sum($result))}}</td>
            </tr>
          <tr>
            <td class="static">Dif.</td>
            <td class="first-col"></td>
            <?php
            foreach($liqByM as $k=>$v):
              $dif = $result[$k]-$v;
              $class = ($dif>0) ? 'td-gren' : 'td-red';
              if ($dif == 0) $class = '';
              ?>
            <td class="{{$class}}">{{moneda($dif)}}</td>
              <?php
            endforeach;
            ?>
            
              <?php 
                $dif = array_sum($result) - array_sum($liqByM);
                $class = ($dif>0) ? 'td-gren' : 'td-red';
                if ($dif == 0) $class = '';
              ?>
            <td class="{{$class}}">{{moneda($dif)}}</td>
          </tr>
        </tfoot>
    </table>
 
</div>
<style>

.table-liq td{
    margin: 0 !important;
    padding: 10px 0 !important;
    min-width: 80px;
}
    
input.form-control.liquidation {
    padding: 2px !important;
        border-left: none;
    text-align: center;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}


tfoot tr td {
    background-color: aliceblue;
    font-weight: 700;
    text-align: center;
}

tfoot tr td.td-red {
    background-color: red;
    color: #FFF;
}
td.td-gren {
    background-color: #55f755;
}
</style>
  