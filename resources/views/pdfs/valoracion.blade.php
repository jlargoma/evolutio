<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>VALORACIÓN DE SALUD Y PREPARACIÓN AL ENTRENAMIENTO</title>
    <!-- Latest compiled and minified CSS & JS -->
    <style type="text/css">
      body{max-width: 800px; margin-top: 25px; margin: auto;}
      .table-borderless {
        border: none;
      }
      .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
        border-style: solid;
      }
      .table > thead > tr > th, .table > tfoot > tr > th {
        padding: 16px 10px 12px;
        font-family: "Source Sans Pro", "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 15px;
        font-weight: 600;
        text-transform: uppercase;
      }
      .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
      }

      .text-center {
        text-align: center;
      }
      .text-right {
        text-align: right;
      }
      th {
        text-align: left;
      }
      .row{
        display: contents;
        clear: both;
        overflow: auto;
      }
      .col-md-6{
        width: 46%;
        display: inline-grid;
        margin: 1%;
      }

      .turnos small{
        margin-left: 1em;
        display: block;
        font-size: 12px;
      }
      h1 {color: #7b7b7b;}
      h1 b {color: #000000;}
      thead{background-color: #cfddef;}
      tfoot{background-color: #f0f0f0;}
      .formValora u { margin: -8px 0 12px !important;}

      .formValora .field {
        min-height: 1.4em !important;}
      .qstion1 .form-material.mt-2 {
        margin: 1em 0;
      }
      .formValora h4.mt-1 {
        color: #000 !important;
        border-bottom: 2px solid  !important;
        border-top: 2px solid !important;
      }
      .formValora h5 {
        font-size: 14px !important;
      }
      .formValora table th {font-size: 14px !important;}
      .mt-1{margin-top: 1em;}
      .mt-2{margin-top: 4px;}
      .btn-print {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        background-color: #19191982;
        left: 0;
        padding: 1em;
      }
      .btn-print button{
        background-color: #d44242;
        border-radius: 4px;
        padding: 10px;
        color: #FFF;
        font-weight: bold;
      }
       @media print {
        .btn-print{display: none;}
      }
    </style>
  </head>
  <body>
    <h1>VALORACIÓN DE SALUD Y PREPARACIÓN AL ENTRENAMIENTO</h1>
    <div class="text-left">
      @include('customers.blocks.valoracion')
    </div>
    <div class="text-center">
      <small >Evolutio <?php echo date('Y'); ?></small>
    </div>
    <div class="btn-print">
      <button onclick="window.print();">Imprimir</button>
      </div>

  </body>
</html>
