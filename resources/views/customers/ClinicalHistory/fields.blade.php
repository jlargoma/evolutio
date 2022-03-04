<div class="fromEncNutri">
  <div class="clearfix"></div>
  <div class="field f1 bT bL">
    <?php Qblock($data, 'hclinic_q1'); ?>
  </div>
  <div class="field f1 bT bL bR ">
    <?php Qblock($data, 'hclinic_q2'); ?>
  </div>
  <div class="field f1 bT bL">
    <?php QDate($data, 'hclinic_q3'); ?>
  </div>
  <div class="field f1 bT bL bR">
    <?php Qblock($data, 'hclinic_q4'); ?>
  </div>
  <div class="field f1 bT bL ">
    <?php Qblock($data, 'hclinic_q5'); ?>
  </div>
  <div class="field f1 bT bL bR">
    <?php Qblock($data, 'hclinic_q6'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php Qblock($data, 'hclinic_q7'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinic_q8'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinic_q9'); ?>
  </div>
  <div class="field f3 bT bL bR">
    <?php Qblock($data, 'hclinic_q10'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php Qblock($data, 'hclinic_q11'); ?>
  </div>
  <div class="field f2 bT bL bR bB">
    <?php Qblock($data, 'hclinic_q12'); ?>
  </div>
  <div class="clearfix"><br/></div>
  <br/>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinic_q13'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinic_q14'); ?>
  </div>
  <div class="field f3 bT bL bR">
    <?php Qblock($data, 'hclinic_q15'); ?>
  </div>
  <div class="field f3 bT bL bB">
    <?php Qblock($data, 'hclinic_q16'); ?>
  </div>
  <div class="field f3 bT bL bB">
    <?php Qblock($data, 'hclinic_q17'); ?>
  </div>
  <div class="field f3 bT bL bR bB">
    <?php Qblock($data, 'hclinic_q18'); ?>
  </div>

  <div class="clearfix"><br/></div>
  <h3>Antecedentes personales:</h3>

  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q19', 'hclinic_q20'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q21', 'hclinic_q22'); ?>
    <?php QblockOpt($data, 'hclinic_q23', 'hclinic_q24'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q25', 'hclinic_q26'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q27', 'hclinic_q28'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q29', 'hclinic_q30'); ?>
  </div>
  <div class="fieldDouble bT bL bR">
    <?php QblockOpt($data, 'hclinic_q31', 'hclinic_q32'); ?>
  </div>
  <div class="fieldDouble bT bL bR bB ">
    <?php QblockOpt($data, 'hclinic_q33', 'hclinic_q34'); ?>
  </div>

  <div class="clearfix"><br/></div>
  <h3>Antecedentes familiares:</h3>
  <div class="p9">
    <?php printPainImg(); ?>
  </div>
  <div class="p9">
    <h4>Responda según su dolor</h4>
    <?php QTextarea($data, 'hclinic_q35'); ?>
  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q36'); ?>
  </div>
  <div class="fieldRadios">
    <?php QblockOption($data, 'hclinic_q37', ['Brusca y rápida', 'Lenta y progresiva']); ?>
  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q38'); ?>
  </div>
  <div class="fieldRadios">
    <?php qLstOptions($data, 'hclinic_q39', 'hclinic_q40',$options['hclinic_q39']); ?>

  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q41'); ?>
  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q42'); ?>
  </div>

  <div class="fieldRadios">
    <div class="fieldRadios">
      <?php QblockOption($data, 'hclinic_q43', ['SI', 'NO']); ?>
    </div>
  </div>

  <div class="fieldDouble">
    <?php QblockOpt($data, 'hclinic_q44', 'hclinic_q45'); ?>
  </div>
  <div class="fieldDouble">
    <?php QblockOpt($data, 'hclinic_q46', 'hclinic_q47'); ?>
  </div>
  <div class="fieldDouble">
    <?php QblockOpt($data, 'hclinic_q48', 'hclinic_q49'); ?>
  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q50'); ?>
  </div>
  <div class="field f2">
    <?php Qblock($data, 'hclinic_q51'); ?>
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
    margin-right: 4px;
    font-weight: 400;
  }

  textarea{
    display: block;
    width: 100%;
    min-height: 73px;
  }


  .p9{
    padding: 9px;
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

  .canvasBox {
    clear: both;
    overflow: auto;
    display: block;
    text-align: center;
    max-width: 630px;
    margin: 2em auto 65px;
  }

  .pain_img-box{
    background-image: url('/public/img/hClinic.jpg');
    width: 320px;
    background-size: contain;
    height: 197px;
    background-repeat: no-repeat;
    float: left;
    border: 2px solid #b3b3b3;
  }
  .canvasText{
    float: left;
    text-align: center;
    width: 310px;
  }

</style>