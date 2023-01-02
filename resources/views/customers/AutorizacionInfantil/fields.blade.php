<div class="fromEncNutri">
  <p>En <?php Qblock($data, 'autoriz_lugar'); ?>, a <?php Qblock($data, 'autoriz_dias_dia', 'f3'); ?> de <?php Qblock($data, 'autoriz_dias_mes', 'f3'); ?> de <?php Qblock($data, 'autoriz_dias_anio', 'f3'); ?></p>
  <br /><br />
  <p>D/Dª <?php Qblock($data, 'autoriz_tutor'); ?> con NIF nº <?php Qblock($data, 'autoriz_tutorNIF', 'f2'); ?>, como PADRE/MADRE o TUTOR, autoriza a asistir al centro deportivo
    EVOLUTIO.FIT y hacer uso de sus instalaciones y actividades a su hijo/a <?php Qblock($data, 'autoriz_menor'); ?></p>
  <p>Menor de edad y con NIF nº <?php Qblock($data, 'autoriz_menorNIF', 'f2'); ?> y asume las obligaciones contractuales
    pactadas en el contrato que también se firma el mismo día de fecha <?php Qblock($data, 'autoriz_ini_dia', 'f3'); ?> de <?php Qblock($data, 'autoriz_ini_mes', 'f3'); ?> de <?php Qblock($data, 'autoriz_ini_anio', 'f3'); ?>.</p>
</div>

<style>
  .fromEncNutri img {
    max-width: 100%;
  }

  .fromEncNutri .radio input[type="radio"] {
    margin-right: 10px;
  }

  .fromEncNutri .radio span {
    margin-top: 0;
  }

  .fromEncNutri .bold {
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
    border: 0px solid #b3b3b3;
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


  .fromEncNutri .bT {
    border-top-width: 1px;
  }

  .fromEncNutri .bB {
    border-bottom-width: 1px;
  }

  .fromEncNutri .bR {
    border-right-width: 1px;
  }

  .fromEncNutri .bL {
    border-left-width: 1px;
  }

  .field input {
    border: none;
    border-bottom: 1px dashed;
  }

  .p9 {
    padding: 9px;
  }

  @media only screen and (max-width: 626px) {

    .field.f1,
    .field.f2,
    .field.f3 {
      width: 100%;
      float: none;
    }

    .fromEncNutri .bL {
      border-right-width: 1px;
    }

    .content-box {
      max-width: 100% !important;
    }

    .fromEncNutri .fieldDouble .f4_1,
    .fromEncNutri .fieldDouble .f4_2 {
      width: 100%;
      float: none;

    }
  }
</style>