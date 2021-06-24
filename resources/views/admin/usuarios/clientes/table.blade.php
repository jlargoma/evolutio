<table class="table table-striped js-dataTable-full-clients table-header-bg">
    <thead>
        <tr>
            <th class="text-center tc0 hidden-xs hidden-sm"></th>
            <th class="text-center tc1">Nombre Cliente<br></th>
            <th class="text-center tc2">Acciones</th>
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
                </td>
                <td class="text-center tc2">
                    <button class="btn btn-default add_rate" data-toggle="modal" data-target="#modalCliente" data-idUser="<?php echo $user->id; ?>">
                        <i class="fa fa-usd" aria-hidden="true"></i>
                    </button>
                </td>
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
                            $textAux.= '<div class="label events label-success openEditCobro" data-cobro="'.$r['cid'].'"  data-id="'.$r['id'].'">';
                          else:
                            $pending = true;
                            $auxPend += $r['price'];
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
</table>