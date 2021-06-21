<?php $result = []; ?>
<div class="col-xs-12 table-responsive">
    <table class="table table-bordered table-liq table-header-bg">
        <thead>
            <tr>
                <th class="text-center"> </th>
                @foreach($aMonths as $k=>$v)
                <th class="text-center">{{$v.' '.$year}}</th>
                @endforeach
                <th class="text-center">Total</th>
                
            </tr>
        </thead>
        <tbody>
            <!-- LIQUIDACIONES -->
            <tr>
                <td class="text-center">
                    <b>PAGOS MENS.</b>
                </td>
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
                <td class="text-center">
                    <b>PAGADO</b>
                </td>
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
            <td >Total</td>
                @foreach($aMonths as $k=>$v)
                <td>
                  {{moneda($result[$k])}}
                </td>
                @endforeach
                <td>{{moneda(array_sum($result))}}</td>
            </tr>
          <tr>
            <td >Dif.</td>
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
<div class="col-xs-12 table-responsive">
    <table class="table table-borderless table-striped table-vtop">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-left">Concepto</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total = $salary;
            ?>
            <tr>
                <td class="text-center"><strong><?php echo $i ?></strong></td>
                <td class="text-left font-s20" style="text-transform: uppercase;">Salario Base</td>
                <td class="text-right font-s20"><strong><?php echo $salary; ?>€</strong></td>
            </tr>
            <?php foreach ($classLst as $key => $pago): ?>
                <?php $i++ ?>
                <tr>
                    <td class="text-center"><strong><?php echo $i ?></strong></td>
                    <td class="text-left font-s18 turnos">
                        
                        <?php echo $classLst[$key]?>
                        <?php 
                        if(isset($pagosClase[$key])):
                            foreach ($pagosClase[$key] as $turno){
                            echo '<small>'.$turno.'</small>';
                            }
                        endif;
                        ?>
                    </td>
                    <td class="text-right font-s20"><strong><?php echo $totalClase[$key] ?>€</strong></td>
                </tr>
                <?php $total += $totalClase[$key]; ?>
            <?php endforeach ?>
            <tr class="success">
                <td colspan="2" class="text-right text-uppercase"><strong>Total:</strong></td>
                <td class="text-right font-s24"><strong><?php echo $total ?>€</strong></td>
            </tr>
        </tbody>
    </table>
</div>

<style>

.table-liq td{
    margin: 0 !important;
    padding: 10px 0 !important;
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
  