<?php 
$auxPay = $auxToPay = [0,0,0];


function printIconRate($uRates){
  global $cFRates;
  $icons = [];
  foreach($uRates as $ur){
    foreach($ur as $rID => $rDetail){
      foreach($cFRates as $item){
        if (in_array($rID,$item->ids)){
          $icons[$item->k] = [$item->name,$item->icon];
        }
      }
    }
  }
  foreach($icons as $i){
    echo '<i class="fa '.$i[1].'" title="'.$i[1].'"></i>';
  }
} 

// var_dump($uRates); die;
?>
<table class="table table-striped js-dataTable-full-clients table-header-bg">
    <thead>
        <tr>
            <th class="text-center tc0 hidden-xs hidden-sm"></th>
            <th class="text-center tc1">Nombre Cliente<br></th>
            <th class="text-center tc2">Acciones</th>
            <th class="text-center tc2">Altas / Bajas</th>
            <th class="text-center tc2">Tarifa</th>
            <th class="text-center tc3">Tel<span class="hidden-xs hidden-sm">éfono</span><br></th>
            <th class="text-center tc4">
                <?php
                $aux = ($month == 1) ? 12 : $month - 1;
                echo $months[$aux];
                ?>
                <label class="text-danger">{{moneda($toPay[0])}}</label>
            </th>
            <th class="text-center tc4">
                <?php
                $aux = $month;
                echo $months[$aux];
                ?>
                <label class="text-danger">{{moneda($toPay[1])}}</label>
            </th>
            <th class="text-center tc4">
                <?php
                $aux = ($month == 12) ? 1 : $month + 1;
                echo $months[$aux];
                ?>
                <label class="text-danger">{{moneda($toPay[2])}}</label>
            </th>
            <th class="text-center sorting_desc hidden-xs hidden-sm" id="estado-payment">Estado</th>
            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $key => $user): ?>
            <tr>
                <td class="text-center hidden-xs hidden-sm tc0">
                    <label class="css-input switch switch-sm switch-success">
                        <?php $checked = ($user->status == 1) ? 'checked' : ''; ?>
                        <input type="checkbox" class="switchStatus" data-id="<?php echo $user->id ?>" <?php echo $checked ?>><span></span>
                    </label>
                </td>
                <td class="text-left tc1"> 
                    <a  class="openUser" data-id="<?php echo $user->id; ?>"  data-type="user" data-original-title="Editar user" ><b><?php echo $user->name; ?></b></a>
                    <?php echo in_array($user->id,$uPlan) ? '<i class="fa fa-heart text-success fidelity"></i>' : '' ?>
                    <?php echo in_array($user->id,$uPlanPenal) ? '<i class="fa fa-heart text-danger fidelity" title="CLEINTE CON PENALIZACION DE 60€ POR BAJA ANTICIPADA"></i>' : '' ?>
                    <?php if ($user->visa == 1) echo  '<i class="fa fa-credit-card text-success"></i>'  ?>
                    <br/>
                    <?php 
                    $aRate = [];
                    for ($i = 0; $i < 3; $i++) if (isset($uRates[$i][$user->id])) $aRate[] = $uRates[$i][$user->id];
                    if (count($aRate) > 0 ) printIconRate($aRate); 
                    ?>
                </td>
                <td class="text-center tc2">
                    <button class="btn btn-default openAdd" data-idUser="<?php echo $user->id; ?>">
                        <i class="fa fa-usd" aria-hidden="true"></i>
                    </button>
                  
                  <div class="boxAddServBono" style="display:none">
                    <button class="btn btn-default add_rate" data-toggle="modal" data-target="#modalCliente" data-iduser="<?php echo $user->id; ?>">
                        <i class="fa fa-usd" aria-hidden="true"></i> Asignar Servicios
                    </button>
                    <button class="btn btn-default add_bono" data-iduser="<?php echo $user->id; ?>">
                      <i class="fa fa-plus-circle" aria-hidden="true"></i> Asignar Bonos
                    </button>
                  </div>
                </td>
                <?php if (array_key_exists($user->id,$aLstAltBaj)) : 
                  $auxRate = $auxActive = [];
                  foreach($aLstAltBaj[$user->id] as $altBaj){
                    $auxRate[] = ($altBaj['active'] == 0) ? 'BAJA' : 'ALTA';
                    if ($altBaj['rt'] == 1) $auxActive[] = 'Membresias';
                    if ($altBaj['rt'] == 2) $auxActive[] = 'P.T.';
                  }
                  
                  ?>
                  <td class="text-center tc3"><?php echo implode('<br>',$auxRate)?></td>
                  <td class="text-center tc3"><?php echo implode('<br>',$auxActive)?></td>
                <?php else : ?>
                  <td class="text-center tc3"></td>
                  <td class="text-center tc3"></td>
                <?php endif; ?>
              
                <td class="text-center tc3">
                    <span class="hidden-xs hidden-sm"><?php echo $user->telefono; ?></span>
                    <span class="hidden-lg hidden-md">
                        <a href="tel:<?php echo $user->telefono; ?>">
                            <i class="fa fa-phone"></i>
                        </a>
                    </span>
                </td>
                <?php 
                $auxMonth = $month - 2;
                $pending = null;
                for ($i = 0; $i < 3; $i++): 
                    $auxMonth++;
                    if ($auxMonth>12) $auxMonth = 1;
                    $textAux = '';
                    $auxPend = 0;
                    if (isset($uRates[$i][$user->id])):
                      if ($pending == null) $pending = false;
                      foreach ($uRates[$i][$user->id] as $rate):
                        foreach ($rate as $r):
                          if($r['paid']):
                            $auxPay[$i] += $r['price'];
                            $textAux.= '<div class="label events label-success openEditCobro" data-cobro="'.$r['cid'].'"  data-id="'.$r['id'].'">';
                          else:
                            $pending = true;
                            $auxPend += $r['price'];
                            $auxToPay[$i] += $r['price'];
                            $textAux.= '<div class="label events label-danger openCobro" data-rate="'.$r['id'].'" data-id="'.$r['id'].'">';
                          endif;
                          $textAux.= moneda($r['price']).'</div>';
                        endforeach;
                      endforeach;
                    endif;
                        ?>
                    <td class="text-center tc4 <?php if($i==1) echo 'yb'; ?>" data-order="<?php echo $auxPend; ?>">
                          
                          <?php echo $textAux; ?>
                    </td>
                <?php endfor; ?>
                <td class="text-center hidden-xs hidden-sm" data-order="<?php echo $pending ? 1 : (($pending === false) ? 0:'');?>" >
                  <?php 
                  if ($pending === false){
                    echo '<i class="fa fa-circle text-success" aria-hidden="true"></i>';
                  }
                  if ($pending){
                    echo '<i class="fa fa-circle text-danger" aria-hidden="true"></i>';
                  }
                  ?>
                </td>

            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot style="display: none;">
      <tr>
        <td colspan="4"></td>
        <?php 
          for ($i = 0; $i < 3; $i++): 
        ?>
        <td>{{$auxPay[$i]}} / {{$auxToPay[$i]}}</td>
        <?php
          endfor;
        ?>
      </tr>
    </tfoot>
</table>