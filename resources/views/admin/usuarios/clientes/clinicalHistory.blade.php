@extends('layouts.popup')


<?php
global $responses;
$responses = $resp;
function Qblock($data, $qID) {
  global $responses;
  ?>
  <label>{{$data[$qID]}}</label>
  <input type="text" id="<?= $qID ?>" name="<?= $qID ?>" value="<?php if (isset($responses[$qID])) echo $responses[$qID]; ?>" requireddd="" class="autosave">
  <?php
}

function QTextarea($data, $qID) {
  global $responses;
  ?>
  <label>{{$data[$qID]}}</label>
  <textarea id="<?= $qID ?>" name="<?= $qID ?>" requireddd="" class="autosave"><?php if (isset($responses[$qID])) echo $responses[$qID]; ?></textarea>
  <?php
}

function QDate($data, $qID) {
  global $responses;
  ?>
  <label>{{$data[$qID]}}</label>
  <input  size="10" maxlength="10" onKeyUp = "this.value = formateafecha(this.value);" placeholder="DD-MM-YYYY" id="{{$qID}}" name="{{$qID}}" value="<?php if (isset($responses[$qID])) echo $responses[$qID]; ?>" requireddd="" class="autosave">
  <?php
}

function QblockOpt($data, $qID, $qIDsub) {
  global $responses;
  $resp = isset($responses[$qID]) ? $responses[$qID] : '';
  ?>
  <h4>{{$data[$qID]}}</h4>
  <div class="field f4_1">
    <div class="radios">
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" <?php if ($resp == 'SI') echo 'checked' ?> value="SI" class="autosave"> <span style="margin-right: 12px;">SI</span>
      <input type="radio" id="<?= $qID ?>" name="<?= $qID ?>" <?php if ($resp == 'NO') echo 'checked' ?> value="NO" class="autosave"> <span>NO</span>
    </div>
  </div>
  <div class="field f4_2">
    <label>{{$data[$qIDsub]}}</label>
    <input type="text" id="<?= $qIDsub ?>" name="<?= $qIDsub ?>" value="<?php if (isset($responses[$qIDsub])) echo $responses[$qIDsub]; ?>" class="autosave">
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
      <div class="">
        <input type="checkbox" name="updImg"> Actualizar la imagen
      </div>
      <button class="btn btn-danger" type="button" id="clearPainImg">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </div>

  </div>
  <?php
}

function QblockOption($data, $qID, $opcion) {
  global $responses;
   $resp = isset($responses[$qID]) ? $responses[$qID] : '';
  ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($opcion as $q):
    ?>
    <div class="radios">
      <input type="radio" id="{{$qID}}" name="{{$qID}}" <?php if ($resp == $q) echo 'checked' ?> value="{{$q}}" class="autosave">{{$q}}
    </div>
    <?php
  endforeach;
}

function qLstOptions($data, $qID, $qID2,$options) {
  global $responses;
  $resp = null;
  if (isset($responses[$qID]))
    $resp = json_decode($responses[$qID], false);
  if (!$resp) $resp = [];
  if (!is_array($resp)) $resp = [$resp];
  ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($options as $k=>$q):
    ?>
    <div class="radios">
      <input type="checkbox" id="hclinic_q39" name="hclinic_q39[]" <?php if (in_array($k, $resp)) echo 'checked' ?> value="{{$k}}" class="autosave">{{$q}}
    </div>
    <?php endforeach; ?>
  <div class="otros">
  <?php echo Qblock($data, $qID2); ?>
  </div>
  <?php
}
?>
@section('content')

<?php
$count = 1;
?>
<div class="boxForm" >
  <h1 class="text-center">EDITAR Hª CLINICA</h1>
  <form class="row" action="{{ url('/admin/setClinicHist') }}" method="post"  id="formClinicHistory"> 
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    @include('customers.ClinicalHistory.fields')


    <div class="col-md-12  mt-1">
      <button class="btn btn-success" type="submit">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar
      </button>
      <a class="btn btn-success" href="" target="_blank" >
        <i class="fa fa-file" aria-hidden="true"></i> Imprimir / Descargar
      </a>
    </div>
  </form>
</div>
<style>

 
</style>
<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">

    function formateafecha(fecha)
    {
        var fecha = fecha.replaceAll('-', '');
        fecha = fecha.replaceAll('D', '');
        fecha = fecha.replaceAll('M', '');
        fecha = fecha.replaceAll('Y', '');
        var long = fecha.length;
        var newDate = '';
        if (long > 9)
            long = 9;
        for (var i = 0; i < long; i++) {
            if (i == 2 || i == 4)
                newDate += '-';
            newDate += fecha[i];
        }
        return newDate;

    }


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
      signaturePad.fromDataURL("/seeImg{{$resp['hclinic_PainImg']}}", optCanvas);
      @endif
      $('#clearPainImg').on('click', function (e) {
      signaturePad.clear();
      });
      $('#formClinicHistory').on('submit', function (e) {
        $('#PainImg').val(signaturePad.toDataURL()); // save image as PNG
      });
    
    $('.autosave').on('change', function () {
      var values = $(this).val();
      var field = $(this).attr('name');
        if($(this).attr('type') == 'checkbox'){
          values = [];
          field = $(this).attr('id');
          $('#'+field+':checked').each(function(){
            values.push($(this).val());
          });
          values = values.join(",,,");
        }
      
      var posting = $.post('/admin/autosaveClinicHist', {
        id: {{$user->id}},
        field: field,
        val: values,
      }).done(function (data) {

      if (data == 'OK'){
        window.show_notif('success', 'campo actualizado');
      } else {
        window.show_notif('error', data);
      }
      });
    });
    
 });

</script>
@endsection