<?php
$count = 1;
?>
<div class="boxFile">
  <h3 class="text-left">ARCHIVO DE NUTRICIÓN</h3>
  <form enctype="multipart/form-data" action="{{ url('/admin/clientes/saveFilesNutri') }}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="uid" value="{{ $user->id }}">
    <div class="form-group">
      <div class="row">
        @if(isset($encNutr['nutri_file']) && $encNutr['nutri_file'] )
        <div class="col-md-5">
          <a href="<?= $encNutr['nutri_file']; ?>" target="_black">Ver Archivo adjunto</a>
        </div>
        <div class="col-md-3">
          <input type="checkbox" name="delFile"> Borrar Archivo
        </div>
        @else
        <div class="col-md-8">
          <input type="file" class="form-control" name="file" >
        </div>
        @endif
        <div class="col-md-4">
          <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
      </div>
    </div>
  </form>
</div>
<h3 class="text-left">ENCUESTA DE NUTRICIÓN</h3>
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
    <button type="button" title="Enviar / Re-enviar mail de encuesta" class="btn btn-info  sendEncuesta" data-id="1897" >
      <i class="fa fa-envelope"></i> Enviar
    </button>
    <a class="btn btn-success" href="{{$encNutr['url_dwnl']}}" target="_blank" >
      <i class="fa fa-file" aria-hidden="true"></i> Imprimir / Descargar
    </a>
  </div>
</form>
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

  .boxFile {
    box-shadow: 1px 1px 5px 2px #000;
    padding: 7px 16px;
    margin: 25px;
}
</style>