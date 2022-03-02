<?php

function Qblock($data, $qID) {
  ?>

  <label>{{$data[$qID]}}</label>
  <input type="text" id="<?= $qID ?>" name="<?= $qID ?>" value="" required="">
  <?php
}

function QblockOpt($data, $qID, $qIDsub) {
  ?>
  <h4>{{$data[$qID]}}</h4>
  <div class="field f4_1">
    <div class="radios">
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" value="SI"> <span style="margin-right: 12px;">SI</span>
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" value="NO"> <span>NO</span>
    </div>
  </div>
  <div class="field f4_2">
    <label>{{$data[$qIDsub]}}</label>
    <input type="text" id="<?= $qIDsub ?>" name="<?= $qIDsub ?>" value="">
  </div>
  <?php
}

$opt_hclinic_q39 = ['Sordo','Profundo','Pulsátil','  Eléctrico','  Punzante','  Agudo',' Localizado','Quemante','Presión'];
?>
<div class="fromEncNutri">
  <div class="clearfix"></div>
  <div class="field f1 bT bL">
    <?php echo Qblock($data, 'hclinic_q1'); ?>
  </div>
  <div class="field f1 bT bL bR ">
    <?php echo Qblock($data, 'hclinic_q2'); ?>
  </div>
  <div class="field f1 bT bL">
    <label>{{$data['hclinic_q3']}}</label>
    <input  size="10" maxlength="10" onKeyUp = "this.value = formateafecha(this.value);" placeholder="DD-MM-YYYY" id="hclinic_q3" name="hclinic_q3" required="">
  </div>
  <div class="field f1 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q4'); ?>
  </div>
  <div class="field f1 bT bL ">
    <?php echo Qblock($data, 'hclinic_q5'); ?>
  </div>
  <div class="field f1 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q6'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q7'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php echo Qblock($data, 'hclinic_q8'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php echo Qblock($data, 'hclinic_q9'); ?>
  </div>
  <div class="field f3 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q10'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q11'); ?>
  </div>
  <div class="field f2 bT bL bR bB">
    <?php echo Qblock($data, 'hclinic_q12'); ?>
  </div>
  <div class="clearfix"><br/></div>
  <br/>
  <div class="field f3 bT bL">
    <?php echo Qblock($data, 'hclinic_q13'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php echo Qblock($data, 'hclinic_q14'); ?>
  </div>
  <div class="field f3 bT bL bR">
    <?php echo Qblock($data, 'hclinic_q15'); ?>
  </div>
  <div class="field f3 bT bL bB">
    <?php echo Qblock($data, 'hclinic_q16'); ?>
  </div>
  <div class="field f3 bT bL bB">
    <?php echo Qblock($data, 'hclinic_q17'); ?>
  </div>
  <div class="field f3 bT bL bR bB">
    <?php echo Qblock($data, 'hclinic_q18'); ?>
  </div>

  <div class="clearfix"><br/></div>
  <h3>Antecedentes personales:</h3>

  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q19', 'hclinic_q20'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q21', 'hclinic_q22'); ?>
    <?php echo QblockOpt($data, 'hclinic_q23', 'hclinic_q24'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q25', 'hclinic_q26'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q27', 'hclinic_q28'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q29', 'hclinic_q30'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php echo QblockOpt($data, 'hclinic_q31', 'hclinic_q32'); ?>
  </div>
  <div class="fieldDouble bT bL bR bB ">
    <?php echo QblockOpt($data, 'hclinic_q33', 'hclinic_q34'); ?>
  </div>

  <div class="clearfix"><br/></div>
  <h3>Antecedentes familiares:</h3>
  <h4>Colorea todas las zonas en las que Usted siente dolor</h4>
  <h4>Responda según su dolor</h4>

  <label>{{$data['hclinic_q35']}}</label>
  <textarea id="hclinic_q35" name="hclinic_q35" value="" required=""></textarea>
  <div class="clearfix"><br/></div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q36'); ?>
  </div>
  <div class="fieldRadios">
    <label>{{$data['hclinic_q37']}}</label>
    <div class="radios">
    <input type="radio" id="hclinic_q37" name="hclinic_q37" value="Brusca y rápida">Brusca y rápida
     </div>
    <div class="radios">
    <input type="radio" id="hclinic_q37" name="hclinic_q37" value="Lenta y progresiva">Lenta y progresiva
    </div>
  </div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q38'); ?>
  </div>
  <div class="fieldRadios">
    <label>{{$data['hclinic_q39']}}</label>
    @foreach($opt_hclinic_q39 as $q)
    <div class="radios">
    <input type="checkbox" id="hclinic_q37" name="hclinic_q37[]" value="{{$q}}">{{$q}}
     </div>
    @endforeach
    <div class="otros">
    <?php echo Qblock($data, 'hclinic_q40'); ?>
    </div>
  </div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q41'); ?>
  </div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q42'); ?>
  </div>

    <div class="fieldRadios">
    <label>{{$data['hclinic_q43']}}</label>
    <div class="radios">
    <input type="radio" id="hclinic_q43" name="hclinic_q43" value="SI">SI
     </div>
    <div class="radios">
    <input type="radio" id="hclinic_q43" name="hclinic_q43" value="NO">NO
    </div>
  </div>
  
  <div class="fieldDouble">
    <?php echo QblockOpt($data, 'hclinic_q44', 'hclinic_q45'); ?>
  </div>
  <div class="fieldDouble">
    <?php echo QblockOpt($data, 'hclinic_q46', 'hclinic_q47'); ?>
  </div>
  <div class="fieldDouble">
    <?php echo QblockOpt($data, 'hclinic_q48', 'hclinic_q49'); ?>
  </div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q50'); ?>
  </div>
  <div class="field f2">
    <?php echo Qblock($data, 'hclinic_q51'); ?>
  </div>
  
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

  .fromEncNutri .fieldDouble {
    margin: 0px !important;
    padding: 3px 8px;
    font-size: 13px;
    min-height: 2.1em;
    margin: 0 !important;
    border:0px solid #b3b3b3;
    padding: 9px;
    clear: both;
    display: block;
    overflow: auto;
  }
  .fromEncNutri .fieldDouble h4{
    font-size: 13px;
  }
  .fromEncNutri .fieldDouble .f4_1{
    float: left;
    width: 120px;
  }
  .fromEncNutri .fieldDouble .f4_2{
    float: left;
    width: calc(100% - 140px);
  }
  .fromEncNutri .field {
    margin: 0px !important;
    padding: 3px 8px;
    font-size: 13px;
    min-height: 2.1em;
    margin: 0 !important;
    border:0px solid #b3b3b3;
    padding: 9px;
    float: left;
    height: 47px;
  }
  .field.f1 {
    width: 50%;
  }
  .field.f2 {
    width: 100%;
  }
  .field.f3 {
    width: 33.333%;
  }
  

  .fromEncNutri .bT{
    border-top-width: 1px;
  }
  .fromEncNutri .bB{
    border-bottom-width: 1px;
  }
  .fromEncNutri .bR{
    border-right-width:  1px;
  }
  .fromEncNutri .bL{
    border-left-width: 1px;
  }

  .field input {
    border: none;
    border-bottom: 1px dashed;
  }

  .fromEncNutri label {
    font-size: 12px;
    margin-right: 12px;
    font-weight: 400;
  }

  textarea{
    display: block;
    width: 100%;
    min-height: 73px;
  }
  
  
  .fieldRadios {
   float: none;
    padding: 9px;
  }
  .fieldRadios label {
    display: block;
  }
  .fieldRadios .otros label {
    display: inline-block;
    margin-left: 6px;
  }
  .fieldRadios .radios {
    display: inline-block;
    margin: 3px 10px 7px 0px;
    font-size: 12px;
  }
  .fieldRadios .radios input{
    margin: 7px;
  }
  .fieldRadios .otros input{
    width: 250px;
    border: none;
    border-bottom: 1px dashed;
  }
</style>