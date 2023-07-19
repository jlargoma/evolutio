@extends('layouts.popup')



@section('externalScripts')
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
</script><!-- comment -->


@endsection

@section('content')

<?php
$opts = [];
function printQuestions($quest,$encNutr){
  $nro = 1;
  foreach($quest as $k=>$v){
    $value = (isset($encNutr[$k])) ? $encNutr[$k] : '';
    echo '<div class="field"><label>'.$nro.'. '. $v.'</label>';
      echo '<input type="text" name="'.$k.'"  id="'.$k.'" value="'.$value.'" class="form-control autosaveNutri" required="">';
      
      echo '</div>';
      $nro++;
  }


}
?>
<div class="boxForm" >
  <h1 class="text-center">EDITAR ENCUESTA DE NUTRICIÓN</h1>
  <form class="row formNutri" action="{{ url('/admin/clientes/setNutri') }}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    <div class="fromEncNutri">

    <h2>Datos personales</h2>
    <?php printQuestions($encNutr['qstion1'],$encNutr); ?>
    <h2>Datos laborales</h2>
    <?php printQuestions($encNutr['qstion2'],$encNutr); ?>
    <h2>Motivo de la consulta </h2>
    <?php printQuestions($encNutr['qstion3'],$encNutr); ?>
    <h2>Historia Ponderal </h2>
    <?php printQuestions($encNutr['qstion4'],$encNutr); ?>
    <h2>Datos clínicos </h2>
    <?php printQuestions($encNutr['qstion5'],$encNutr); ?>
    <h2>Historial Dietético </h2>
    <?php printQuestions($encNutr['qstion6'],$encNutr); ?>
    <h2>Temas digestivos</h2>
    <?php printQuestions($encNutr['qstion7'],$encNutr); ?>
    <h2>Si es mujer</h2>
    <?php printQuestions($encNutr['qstion8'],$encNutr); ?>
    <h2>Describir un día estándar en su semana y un día estándar en el fin de semana (Recuerdo 24h)</h2>
    <div class="field">
    <textarea name="nutri2_q9_1" id="nutri2_q9_1" class="form-control autosaveNutri" rows="10">{{show_isset('nutri2_q9_1',$encNutr)}}</textarea>
    </div>
    <h2>Antropometria</h2>
    <?php printQuestions($encNutr['qstion10'],$encNutr); ?>




    </div>



    <div class="col-md-12  mt-1">
      <button class="btn btn-success" type="submit">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar
      </button>
      <a class="btn btn-success" href="{{$encNutr['url_dwnl']}}" target="_blank" >
        <i class="fa fa-file" aria-hidden="true"></i> Imprimir / Descargar
      </a>
    </div>
  </form>
</div>
<style>

  .formNutri th {
    font-size: 1.32em !important;
    background-color: #46c37b;
    border-color: #34a263;
    padding: 6px !important;
    text-align: center;
    margin-bottom: 1em;
    color: #FFF;
  }


  .formNutri .field {
    display: block;
    clear: both;
    overflow: hidden;
    width: 96%;
    margin: 8px auto;
  }

  .boxForm {
    background-color: #363636;
    padding: 3em 2em;
    color: #FFF;
    border-radius: 13px;
    max-width: 610px;
    margin: 1em auto;

  }
</style>
<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $('.autosaveNutri').on('change', function () {
      var posting = $.post('/admin/clientes/autosaveNutri', {
      id: {{$user-> id}},
        field: $(this).attr('name'),
        val: $(this).val(),
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