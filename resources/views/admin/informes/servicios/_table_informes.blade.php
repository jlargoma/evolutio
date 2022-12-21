<div class="col-md-12 col-xs-12">
    <table class="table table-striped table-header-bg">
        <thead>
        <tr>
            <th class="text-center sorting_disabled"></th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Nombre cliente</th>
            <th class="text-center">Cuota / Concepto</th>
            <th class="text-center">Familia</th>
            <th class="text-center">Importe</th>
            <th class="text-center">MES</th>
            <th class="text-center">Forma pago</th>
            <th class="text-center">ENTRENADOR/FISIO</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($uRates as $ur): $charge = isset($chargesData[$ur->id]) ? $chargesData[$ur->id] : null;?>
        <tr>
            <td class="text-center sorting_disabled"><?php echo ($charge) ? $charge['id'] : '';?></td>
            <td class="text-center"><?php echo ($charge) ? '<b>'.$charge['date'].'</b>' : '';?>  </td>  
            <td class="text-center">
                <?php
                echo (isset($aCustomers[$ur->id_user])) ? $aCustomers[$ur->id_user] : ' - ';
                ?>
            </td>
            <td class="text-center">
                <?php
                if ($ur->id_rate>0) echo (isset($aRates[$ur->id_rate])) ? $aRates[$ur->id_rate] : ' - ';
                if ($ur->bono_id>0) echo (isset($aBonos[$ur->bono_id])) ? $aBonos[$ur->bono_id] : ' - ';
                ?>
            </td>
            <td class="text-center">
                <?php
                echo (isset($aTRates[$ur->id_rate])) ? $aTRates[$ur->id_rate] : ' - ';
                ?>
            </td>
            <td class="text-center"><?php echo ($charge) ? $charge['import'] : moneda($ur->price,false,1);?></td>
            <td class="text-center">
            <?php 
                show_isset($ur->rate_month,$months);
              ?>
            </td>
            <td class="text-center"><?php echo ($charge) ? $charge['type_payment'] : '';?></td>
            <td class="text-center">
              <?php 
               $coach = '--';
               if (isset($aCoachs[$ur->coach_id])){
                 $coach = $aCoachs[$ur->coach_id];
               }
               echo $coach;
               ?>
            </td>
        </tr>
		<?php endforeach ?>
        </tbody>
    </table>
</div>