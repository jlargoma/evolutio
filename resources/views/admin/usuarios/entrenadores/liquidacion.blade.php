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
<div class="col-xs-12 table-responsive">
    <table class="table table-bordered table-striped table-header-bg">
        <thead>
            <tr>
                <th class="text-center"> </th>
                @foreach($aMonths as $k=>$v)
                <th class="text-center">{{$v.' '.$year}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- LIQUIDACIONES -->
            <tr>
                <td class="text-center">
                    <b>PAGOS MENS.</b>
                </td>
                @foreach($aMonths as $k=>$v)
                <td class="text-center">
                    <input type="number" 
                           data-k="{{$k}}" 
                           class="form-control liquidation" 
                           value="{{show_isset($k,$liqLst)}}">
                </td>
                @endforeach
            </tr>

            <tr>
                <td colspan="12" class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2;">
                    <h2 class="text-center">TOTAL DE GASTO ANUAL </h2>
                </td>
                <td class="text-center">
                    <h2 class="text-center">
                        <?php echo $anual; ?>€
                    </h2>
                </td>
            </tr>
        </tbody>
    </table>
</div>