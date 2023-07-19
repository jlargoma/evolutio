<?php
$count = 1;
$opts = [];
function printQuestions($quest,$opt){
  $nro = 1;
  foreach($quest as $k=>$v){
    echo '<div class="field"><label>'.$nro.'. '. $v.'</label>';
    if(isset($opt[$k])){
      foreach($opt[$k] as $i2=>$q2){
        echo '<div class="radio">';
        echo '<input type="radio" value="'.$q2.'" name="'.$k.'"  id="'.$k.'" required=""><span>'.$q2.'</span>';
        echo '</div>';
      }
    }
    else{
      echo '<input type="text" name="'.$k.'"  id="'.$k.'" value="" class="form-control" required="">';
      }
      echo '</div>';
      $nro++;
  }


}
?>
<div class="fromEncNutri">
  <h2>Datos personales</h2>
  <?php printQuestions($data['qstion1'],$opts); ?>
  <h2>Datos laborales</h2>
  <?php printQuestions($data['qstion2'],$opts); ?>
  <h2>Motivo de la consulta </h2>
  <?php printQuestions($data['qstion3'],$opts); ?>
  <h2>Historia Ponderal </h2>
  <?php printQuestions($data['qstion4'],$opts); ?>
  <h2>Datos clínicos </h2>
  <?php printQuestions($data['qstion5'],$opts); ?>
  <h2>Historial Dietético </h2>
  <?php printQuestions($data['qstion6'],$opts); ?>
  <h2>Temas digestivos</h2>
  <?php printQuestions($data['qstion7'],$opts); ?>
  <h2>Si es mujer</h2>
  <?php printQuestions($data['qstion8'],$opts); ?>
  <h2>Describir un día estándar en su semana y un día estándar en el fin de semana (Recuerdo 24h)</h2>
  <div class="field">
  <textarea name="nutri2_q9_1" id="nutri2_q9_1" class="form-control" rows="10"></textarea>
  </div>
  <h2>Antropometria</h2>
  <?php printQuestions($data['qstion10'],$opts); ?>
</div>

<style>
  .fromEncNutri img{
    max-width: 100%;
  }

  .fromEncNutri .radio input[type="radio"] {
    margin-right: 10px;
  }
  .fromEncNutri .radio span {
    margin-top: 0;
  }

  .fromEncNutri .bold{
    text-align: center;
    font-weight: bold;
    color: #000;
    margin-bottom: 2em;
  }

  .fromEncNutri .field {
    margin: 1px 0px 10px !important;
    padding: 3px 8px;
    font-size: 13px;
    min-height: 2.1em;
  }
  .fromEncNutri label {
    font-size: 14px;
  }
  .fromEncNutri .tqstion2 th{
    width: 25%;
    padding: 18px 5px !important;
    font-size: 13px !important;
  }
  h2 {
    color: #00983d;
}

</style>