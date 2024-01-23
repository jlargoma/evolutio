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
<table style="width: 100%;" class="table table-striped js-dataTable-full-clients table-header-bg">
    <thead>
        <tr>
            <th class="text-center tc0 hidden-xs hidden-sm"></th>
            <th class="text-center tc1">Nombre Cliente<br></th>
            
            <th class="text-center tc3">Tel<span class="hidden-xs hidden-sm">éfono</span><br></th>
            
            <th class="text-center tc4" id="estado-payment">Convenio</th>
            
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
                
                
              
                <td class="text-center tc3">
                    <span class="hidden-xs hidden-sm"><?php echo $user->telefono; ?></span>
                    <span class="hidden-lg hidden-md">
                        <a href="tel:<?php echo $user->telefono; ?>">
                            <i class="fa fa-phone"></i>
                        </a>
                    </span>
                </td>
                
                <td class="text-center tc4" >
                  <div class="form-material mt-5">
                    <select data-user="{{$user->id}}" class="form-control convenio-select">
                      <option value="">Ninguno</option>
                      @foreach($convenios as $oConv)
                      <option value="{{$oConv->id}}" @if( $oConv->id == $user->convenio) selected @endif >{{$oConv->name}}</option>
                      @endforeach
                    </select>
                    <label for="convenio">Convenio</label>
                  </div>
                </td>

            </tr>
        <?php endforeach ?>
    </tbody>
</table>