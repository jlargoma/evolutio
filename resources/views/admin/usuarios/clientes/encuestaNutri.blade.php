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
$count = 1;
?>
<div class="boxForm" >
  <h1 class="text-center">EDITAR ENCUESTA DE NUTRICIÓN</h1>
  <form class="row formNutri" action="{{ url('/admin/clientes/setNutri') }}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    <div class="fromEncNutri">
      <?php $nro = 1; ?>
      @foreach($encNutr['qstion1'] as $i=>$q)
      <div class="field">
        <label>{{$nro.'. '.$q}}</label>
        <?php
        switch ($i) {
          case 'nutri_q22':
            ?>
        <div class="table-responsive">
            <table class="table">
              <tr>
                <td></td>
                <th class="text-center">Entre semana</th>
                <th class="text-center">Fines de semana</th>
              </tr>
              <tr>
                <th>Desayuno</th>
                <td><input type="text" id="nutri_q22_1_1" name="nutri_q22_1_1" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_1_1',$encNutr)}}"></td>
                <td><input type="text" id="nutri_q22_2_1" name="nutri_q22_2_1" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_2_1',$encNutr)}}"></td>
              </tr>
              <tr>
                <th>Comida</th>
                <td><input type="text" id="nutri_q22_1_2" name="nutri_q22_1_2" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_1_2',$encNutr)}}"></td>
                <td><input type="text" id="nutri_q22_2_2" name="nutri_q22_2_2" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_2_2',$encNutr)}}"></td>
              </tr>
              <tr>
                <th>Cena</th>
                <td><input type="text" id="nutri_q22_1_3" name="nutri_q22_1_3" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_1_3',$encNutr)}}"></td>
                <td><input type="text" id="nutri_q22_2_3" name="nutri_q22_2_3" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_2_3',$encNutr)}}"></td>
              </tr>
              <tr>
                <th>Snacks / Entrehoras</th>
                <td><input type="text" id="nutri_q22_1_4" name="nutri_q22_1_4" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_1_4',$encNutr)}}"></td>
                <td><input type="text" id="nutri_q22_2_4" name="nutri_q22_2_4" class="form-control autosaveNutri" required="" value="{{show_isset('nutri_q22_2_4',$encNutr)}}"></td>
              </tr>
            </table>
          </div>
            <?php
            break;
          case 'nutri_q2':
            ?>

            <input  size="10" maxlength="10" onKeyUp = "this.value = formateafecha(this.value);" placeholder="DD-MM-YYYY" id="{{$i}}" name="{{$i}}" class="form-control autosaveNutri" required=""  value="{{show_isset($i,$encNutr)}}"></td>
            <?php
            break;
          default :
            ?>
            @if(isset($encNutr['options'][$i]))
            <?php $optValue = isset($encNutr[$i]) ? $encNutr[$i] : ''; ?>
            <select name="{{$i}}"  id="{{$i}}" required="" class="form-control autosaveNutri">
              <option></option>
              @foreach($encNutr['options'][$i] as $i2=>$q2)
              <option value='{{$q2}}' <?php if ($optValue == $q2) echo 'selected' ?>>{{$q2}}</option>
              @endforeach
            </select>
            @else
            <input type="text" id="{{$i}}" name="{{$i}}"  class="form-control autosaveNutri" required=""  value="{{show_isset($i,$encNutr)}}"></td>
            @endif
            <?php
            break;
        }
        $nro++;
        ?>

      </div>
      @endforeach


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