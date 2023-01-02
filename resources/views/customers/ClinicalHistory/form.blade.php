@extends('layouts.app')


<?php
function Qblock($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <input type="text" id="<?= $qID ?>" name="<?= $qID ?>" value="" required="">
  <?php
}
function QTextarea($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <textarea id="<?= $qID ?>" name="<?= $qID ?>" value="" required=""></textarea>
  <?php
}

function QDate($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <input  size="10" maxlength="11" onKeyUp = "this.value = window.inputDate(this.value);" placeholder="DD-MM-YYYY" id="{{$qID}}" name="{{$qID}}" required="">
  <?php
}

function QblockOpt($data, $qID, $qIDsub) {
  ?>
  <h4>{{$data[$qID]}}</h4>
  <div class="field f4_1">
    <div class="radios">
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" value="SI" required=""> <span style="margin-right: 12px;">SI</span>
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" value="NO" required=""> <span>NO</span>
    </div>
  </div>
  <div class="field f4_2">
    <label>{{$data[$qIDsub]}}</label>
    <input type="text" id="<?= $qIDsub ?>" name="<?= $qIDsub ?>" value="">
  </div>
  <?php
}

function printPainImg() {
  ?>
  <div class="canvasBox">
    <input type="hidden" name="PainImg"  id="PainImg" value="">
    <div class="pain_img-box">
      <canvas width="320" height="195" id="cSign"></canvas>
    </div>
    <div class="canvasText">
      <h4>Colorea todas las zonas en las que Usted siente dolor</h4>
      <button class="btn btn-danger" type="button" id="clearPainImg">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </div>

  </div>
  <?php
}

function QblockOption($data, $qID, $opcion) {
  ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($opcion as $q):
    ?>
    <div class="radios">
      <input type="radio" id="{{$qID}}" name="{{$qID}}" value="{{$q}}" required="">{{$q}}
    </div>
    <?php
  endforeach;
}

function qLstOptions($data, $qID,$qID2,$options){
 ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($options as $k=>$q):
    ?>
    <div class="radios">
      <input type="checkbox" id="hclinic_q39" name="hclinic_q39[]" value="{{$k}}">{{$q}}
    </div>
  <?php endforeach; ?>
    <div class="otros">
      <?php echo Qblock($data,$qID2); ?>
    </div>
    <?php
  
}

 ?>
@section('content')
<h1>Hª CLINICA</h1>
@if (isset($already))
<div class="alert alert-success">
  Completada
</div>
@else

@if (session('error'))
<div class="alert alert-danger">
  {{ session('error') }}
</div>
@endif
@if (session('success'))
<div class="alert alert-success">
  {{ session('success') }}
</div>
@else
<form  action="/historia-clinica" method="post" style="margin: 1em auto;" id="formClinicHistory"> 
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="_code" value="<?php echo $code; ?>">
  <input type="hidden" name="_control" value="<?php echo $control; ?>">
  <div class="text-left">
    @include('customers.ClinicalHistory.fields')
  </div>
  <button class="btn btn-success">
    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
  </button>
</form>

@endif
@endif

@endsection
@section('scripts')
<style>
  .container {
    font-size: 14px;
  }
  .content-box{
    max-width: 840px;
  }
  h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
  }
  h2 {
    font-weight: bold;
    font-size: 19px;
    margin-top: 2em;
  }
  h3 {
    font-size: 18px;
    font-weight: bold;
    margin-top: 2em;
    border-bottom: 2px solid;
  }
  ul {
    padding-left: 40px;
  }
  li {
    list-style: disc;
    text-align: left;
    margin: auto;
  }

  .radio {
    margin-left: 2em;
  }
  @media screen {
    .paging h1{
      padding-top: 2em;
    }
    .printBreak,.block-logo{
      display: none;
    }
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">

      Date.prototype.isValid = function () {
          // An invalid date object returns NaN for getTime() and NaN is the only
          // object not strictly equal to itself.
          return this.getTime() === this.getTime();
      };  

    $(document).ready(function () {
      $('.fromEncNutri .field').each(function () {
        var width = $(this).width();
        var label = $(this).find('label').width();
        $(this).find('input').width(width - label - 35);
      });
      var canvas = document.querySelector("canvas");
      var optCanvas = {
      penColor: 'rgb(255, 59, 53, 0.3)',
        dotSize: 9
      };
      var signaturePad = new SignaturePad(canvas, optCanvas);
      var optCanvas = {
      ratio: 1,
        width: 320,
        height: 195,
      };
      @if (isset($resp) && $resp['hclinic_PainImg'])
      signaturePad.fromDataURL("/historia-clinica-imagen/{{$resp['hclinic_PainImg']}}", optCanvas);
      @endif
      $('#clearPainImg').on('click', function (e) {
        signaturePad.clear();
      });
      
      $('#formClinicHistory').on('submit', function (e) {
        $('#PainImg').val(signaturePad.toDataURL()); // save image as PNG
      });
    
    
//    $('.fromEncNutri .field').each(function () {
//    var width = $(this).width();
//    if($(this).find('input').attr('type') == 'text')
//      $(this).find('input').val('Campo: '+$(this).find('label').text());
//    });
    
 });

</script>
@endsection