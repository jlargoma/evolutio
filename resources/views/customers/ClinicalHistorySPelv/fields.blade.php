<div class="fromEncNutri">
  <div class="clearfix"></div>
  <div class="field f1 bT bL">
    <?php Qblock($data, 'hclinicSP_q1'); ?>
  </div>
  <div class="field f1 bT bL bR ">
    <?php Qblock($data, 'hclinicSP_q2'); ?>
  </div>
  <div class="field f1 bT bL">
    <?php QDate($data, 'hclinicSP_q3'); ?>
  </div>
  <div class="field f1 bT bL bR">
    <?php Qblock($data, 'hclinicSP_q4'); ?>
  </div>
  <div class="field f1 bT bL ">
    <?php Qblock($data, 'hclinicSP_q5'); ?>
  </div>
  <div class="field f1 bT bL bR">
    <?php Qblock($data, 'hclinicSP_q6'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php Qblock($data, 'hclinicSP_q7'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinicSP_q8'); ?>
  </div>
  <div class="field f3 bT bL">
    <?php Qblock($data, 'hclinicSP_q9'); ?>
  </div>
  <div class="field f3 bT bL bR">
    <?php Qblock($data, 'hclinicSP_q10'); ?>
  </div>
  <div class="field f2 bT bL bR">
    <?php Qblock($data, 'hclinicSP_q11'); ?>
  </div>
  <div class="field f2 bT bL bR bB">
    <?php Qblock($data, 'hclinicSP_q12'); ?>
  </div>
  <div class="clearfix"><br/></div>
  <br/>
  @for($i=13; $i<25; $i++)
  <div class="fieldTextArea">
    <?php QTextarea($data, 'hclinicSP_q' . $i); ?>
  </div>
  @endfor
</div>

<br/>


<style>

  .fromEncNutri .bold{
    text-align: center;
    font-weight: bold;
    color: #000;
    margin-bottom: 2em;
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
  .fromEncNutri  .fieldTextArea {
    padding: 7px;
    border: 1px solid #b3b3b3;
    border-bottom: none;
  }
  .fromEncNutri  .fieldTextArea:last-child {
    border-bottom: 1px solid #b3b3b3;
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
  .field.text {
    width: 100%;
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
    padding: 3px;
  }


  .p9{
    padding: 9px;
  }

  @media only screen and (max-width: 626px){
    .field.f1,.field.f3 {
      width: 100%;
      border:1px solid #b3b3b3;
    }
    .content-box {
      max-width: 100% !important;
    }
  }
</style>