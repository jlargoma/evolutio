<div class="col-md-12 col-xs-12">
    <table class="table table-striped table-header-bg">
        <thead>

        <tr>
            <th class="text-center sorting_disabled"></th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Nombre cliente</th>
            <th class="text-center">Bono</th>
            <th class="text-center">Importe</th>
            <th class="text-center">MES</th>
            <th class="text-center">Forma pago</th>
            <th class="text-center">ENTRENADOR/FISIO</th>
        </tr>
        </thead>
        <tbody id="tableBonos">
		<?php foreach ($oLstBonos as $charge): 
            $bTypePay = 'PENDIENTE';
            switch ($charge->type_payment){
              case 'banco':
                $bTypePay = 'BANCO';
                break;
              case 'cash':
                $bTypePay = 'METALICO';
                break;
              case 'card':
                $bTypePay = 'TARJETA';
                break;
              case 'bono':
                $bTypePay = 'BONO';
                break;
            }
      ?>
        <tr class="payType_{{$bTypePay}}">
            <td class="text-center sorting_disabled">{{$charge->id}}</td>
            <td class="text-center"><b>{{dateMin($charge->date_payment)}}</b></td>
            <td class="text-center">{{$charge->username}}</td>
            <td class="text-center">
                <?php
                if ($charge->bono_id>0) echo (isset($aBonos[$charge->bono_id])) ? $aBonos[$charge->bono_id] : ' - ';
                ?>
            </td>
            <td class="text-center">{{moneda($charge->import,false,1)}}</td>
            <td class="text-center">
              <?php 
              if(isset($aURates[$charge->id])){
                $monthAux = $aURates[$charge->id];
                show_isset($monthAux,$months);
              }
              ?>
            </td>
            <td class="text-center">{{$bTypePay}}</td>
            <td class="text-center">
                <?php 
                $coach = '--';
                if (isset($aCargesCoachs[$charge->id])){
                  $aux = $aCargesCoachs[$charge->id];
                  $coach = isset($aCoachs[$aux]) ? $aCoachs[$aux]: '--'; 
                }
                echo $coach;
                ?>
            </td>
        </tr>
		<?php endforeach ?>
        </tbody>
    </table>
</div>