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
?>
@section('content')

<?php
$count = 1;
?>
<div class="boxForm" >
  <h1 class="text-center">EDITAR Hª CLINICA  SUELO PÉLVICO</h1>
  <form class="row" action="{{ url('/admin/setClinicHistSPelv') }}" method="post"  id="formClinicHistory"> 
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    @include('customers.ClinicalHistorySPelv.fields')


    <div class="col-md-12  mt-1">
      <button class="btn btn-success" type="submit">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar
      </button>
      <a class="btn btn-success" href="/admin/ver-historia-clinica/{{$resp['urlCode']}}"  >
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
      
      var posting = $.post('/admin/autosaveClinicHistSPelv', {
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