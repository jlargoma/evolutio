<table class="table table-striped js-dataTable-full-clients table-header-bg">
    <thead>
        <tr>
            <th class="text-center hidden-xs hidden-sm sorting_disabled"></th>
            <th class="text-center">Nombre<br></th>
            <th class="text-center sorting_disabled">Tel<span class="hidden-xs hidden-sm">éfono</span><br></th>
            <th class="text-center hidden-xs hidden-sm sorting_disabled">Entrenador<br></th>
            <th class="text-center hidden-xs hidden-sm">
                <?php
                $aux = ($month == 1) ? 12 : $month - 1;
                echo $months[$aux];
                ?>
                <label class="text-danger">
                    (<?php echo $payments[0] ?>)
                </label>
            </th>
            <th class="text-center hidden-xs hidden-sm">
                <?php
                $aux = $month;
                echo $months[$aux];
                ?>
                <label class="text-danger">
                    (<?php echo $payments[1] ?>)
                </label>
            </th>
            <th class="text-center hidden-xs hidden-sm">
                <?php
                $aux = ($month == 12) ? 1 : $month + 1;
                echo $months[$aux];
                ?>
                <label class="text-danger">
                    (<?php echo $payments[2] ?>)
                </label>
            </th>
            <th class="text-center sorting_desc" id="estado-payment">Estado</th>
            <th class="text-center sorting_disabled">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $key => $user): ?>
            <tr>
                <td class="text-center hidden-xs hidden-sm" style="width: 60px!important">
                    <label class="css-input switch switch-sm switch-success">
                        <?php $checked = ($user->status == 1) ? 'checked' : ''; ?>
                        <input type="checkbox" class="switchStatus" data-id="<?php echo $user->id ?>" <?php echo $checked ?>><span></span>
                    </label>
                </td>
                <td class="text-justify"> 
                    <a  class="openUser" data-id="<?php echo $user->id; ?>"  data-type="user" data-original-title="Editar user" ><b><?php echo $user->name; ?></b></a>
                </td>
                <td class="text-center">
                    <span class="hidden-xs hidden-sm"><?php echo $user->telefono; ?></span>
                    <span class="hidden-lg hidden-md">
                        <a href="tel:<?php echo $user->telefono; ?>">
                            <i class="fa fa-phone"></i>
                        </a>
                    </span>
                </td>
                <td class="text-center">
                    <?php 
                    if ($user->userCoach){
                    show_isset($user->userCoach->id_coach,$aCoachs);
                    } else  echo '-';
                    ?>
                </td>
                <?php 
                $auxMonth = $month - 2;
                for ($i = 0; $i < 3; $i++): 
                    $auxMonth++;
                    if ($auxMonth>12) $auxMonth = 1;
                    $textAux = '';
                    $auxPend = 0;
                    if (isset($uRates[$i][$user->id])):
                      foreach ($uRates[$i][$user->id] as $rate):
                        foreach ($rate as $r):
                          if($r['paid']):
                            $textAux.= '<div class="label label-success openEditCobro" data-id="'.$r['cid'].'" data-appointment="'.$r['appointment'].'" >';
                          else:
                            $auxPend += $r['price'];
                            $textAux.= '<div class="label label-danger openCobro" data-rate="'.$r['id'].'" data-appointment="'.$r['appointment'].'" >';
                          endif;
                          $textAux.= moneda($r['price']).'</div>';
                        endforeach;
                      endforeach;
                    endif;
                        ?>
                    <td class="text-center" data-order="<?php echo $auxPend; ?>">
                          
                          <?php echo $textAux; ?>
                    </td>
                <?php endfor; ?>

                <td class="text-center"></td>
                <td class="text-center">
                    <button class="btn btn-default btn-rate-charge" data-toggle="modal" data-target="#modalCliente" data-idUser="<?php echo $user->id; ?>">
                        <i class="fa fa-usd" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>