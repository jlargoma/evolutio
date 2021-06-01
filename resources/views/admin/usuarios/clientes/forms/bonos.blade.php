<h3 class="text-left">Bonos del Usuario
  <button class="btn btn-default add_bono" data-idUser="<?php echo $user->id; ?>">
    <i class="fa fa-usd" aria-hidden="true"></i>
  </button>
</h3>
<?php
$oBonoLst = $user->bonos;
if (count($oBonoLst) > 0) {
  $bNames = [];
  $used = $notUsed = [];
  foreach ($oBonoLst as $b) {
    $class = ($b->charges_to) ? 'used' : 'no_used';
    if (!isset($bNames[$b->id_bono]))
      $bNames[$b->id_bono] = $b->bono->name;
    
    if ($b->charges_to){
      $uRate = \App\Models\UserRates::where('id_charges',$b->charges_to)->first();
      $rateName = ($uRate && $uRate->rate) ? $uRate->rate->name : ' -- ';
      $used[] = [
          $bNames[$b->id_bono],$rateName,convertDateToShow_text($b->updated_at)
      ];
    } else {
      $notUsed[] = [
          $bNames[$b->id_bono],moneda($b->price),convertDateToShow_text($b->created_at)
      ];
    }
    
    
  }
  ?>

  <h3 class="mt-1em">Bonos Disponibles</h3>
  <div class="row">
  <?php
  foreach ($notUsed as $b) {
    ?>
    <div class="col-md-6 col-xs-12">
      <div class="checkBono" >
        <label>{{$b[0]}}: <b>{{$b[1]}}</b></label>
        <span>{{$b[2]}}</span>
      </div>
    </div>
    <?php
  }
  ?>
  </div>
    <div class="line"></div>
  <h3 class="mt-1em">Bonos Usados</h3>
  <div class="row"><?php
  foreach ($used as $b) {
    ?>
    <div class="col-md-6 col-xs-12">
      <div class="checkBono" >
        <label>{{$b[0]}}</label>
        <span>Usado el {{$b[2]}}: <b>{{$b[1]}}</b></span>
      </div>
    </div>
    <?php
  }
  ?></div><?php
}
