<?php
$csrf_token = csrf_token();
?>

<div class="row">
  <div class="col-md-8">
    <div class="boxFile">
      <h3 class="text-left">ARCHIVOS DE FISIOTERAPIA</h3>
      <form enctype="multipart/form-data" action="{{ url('/admin/clientes/saveFiles') }}" method="post">
        <input type="hidden" name="_token" value="{{ $csrf_token }}">
        <input type="hidden" name="uid" value="{{ $user->id }}">
        <input type="hidden" name="type" value="fisio">
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <input type="text" name="fileName" class="form-control" placeholder="Nombre dell archivo" required="">
            </div>
            <div class="col-md-3">
              <label class="custom-file-upload">
                <input type="file" name="file"/>
                <i class="fa fa-cloud-upload"></i> Subir Archivo
              </label>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
          </div>
        </div>
      </form>


      @if(isset($filesFisio) && count($filesFisio) )
      <form action="{{ url('/admin/clientes/delFiles') }}" method="post" id="delFiles">
        <input type="hidden" name="_token" value="{{ $csrf_token }}">
        <input type="hidden" name="uid" value="{{ $user->id }}">
        <input type="hidden" name="fid" id="fileID">
      </form>
      <table class="table">
        @foreach($filesFisio as $k=>$v)
        <tr>
          <th style="width: 80%;">{{$v['name']}}</th>
          <td>
            <button class="btn btn-danger delFileNutri" title="Borrar Archivo" data-k="{{$k}}"><i class="fa fa-trash"></i></button>
          </td>
          <td>
            <a class="btn btn-info" href="<?= $v['url']; ?>" target="_black" title="Ver Archivo"><i class="fa fa-eye"></i></a>
          </td>
        </tr>
        @endforeach
      </table>
      @endif
    </div>
  </div>
  <div class="col-md-4  ">
       <div class="boxFile">
      <h3 class="text-center">Historia Clínica</h3>

      <div class="text-center mb-3">
      @if($seeClinicalHistory)
        <a href="{{$seeClinicalHistory}}" class="btn btn-success" target="_black">
          <i class="fa fa-eye"></i> Ver
        </a>
      @endif
        <a href="/admin/editar-historia-clinica/{{$user->id}}" class="btn btn-info" target="_black">
          <i class="fa fa-pencil"></i> Editar
        </a>
      </div>
       <div class="text-center">
        <a href="#" class="btn  btn-success clearClinicHist" data-id="{{$user->id}}"  type="button" >
          <i class="fa fa-trash"></i> Vaciar
        </a>
        <a href="#"  class="btn btn-success sendClinicHist" data-id="{{$user->id}}"  type="button" >
          <i class="fa fa-envelope"></i> Reenviar
        </a>
      </div>
    </div>
  </div>
</div>







<hr class="line">





<h3 class="text-left">ANOTACIONES</h3>
<div class="row blockNote">
  <div class="col-md-8 col-xs-12 ">
    <?php
    if ($oNotes):
      foreach ($oNotes as $v):
        if ($v->type == 'fisio') {
          $dateTime = strtotime($v->created_at);
          $type = '';
          switch ($v->profile) {
            case 'teach_nutri':
            case 'nutri':
              $type = 'Nutrición';
              break;
            case 'fisio': $type = 'Fisioterapeuta';
              break;
            case 'pt':
            case 'teach':
              $type = 'Entr. Pers.';
              break;
            default: $type = 'Otros';
              break;
          }
          $personal = isset($allCoachs[$v->id_coach]) ? $allCoachs[$v->id_coach] : '-';
          ?>
          <div>
            <div class="row">
              <div class="col-md-8"><b>{{$personal}}</b> ({{$type}})</div>
              <div class="col-md-4">
                {{convertDateToShow_text(date('Y-m-d',$dateTime),true)}}
                <button class="btn editNote" data-id="{{$v->id}}" data-note="{{$v->note}}" data-coach="{{$v->id_coach}}">Editar</button>
              </div>
            </div>
            <p>{{$v->note}}</p>
          </div>
          <hr>
          <?php
        }
      endforeach;
    endif;
    ?>
  </div>
  <div class="col-md-4 col-xs-12">
    <form  action="{{ url('/admin/usuarios/notes') }}" method="post">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="uid" value="{{ $user->id }}">
      <input type="hidden" name="id"  value="">
      <input type="hidden" name="type" value="fisio">
      <div class="form-simple">
        <label for="name">Personal</label>
        <select class="form-control" name="coach" required>
          <option value="">-Personal</option>
          @foreach($allCoachs as $id=>$c)
          <option value="{{$id}}" @if($id == $u_current) selected @endif>{{$c}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-simple">
        <label for="name">Nota</label>
        <textarea name="note" class="form-control" style="min-height: 50vh; border: 1px solid #cecece;padding: 9px;"></textarea>
      </div>
      <button class="btn btn-success" type="submit">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
      </button>
      <button class="btn btn-danger delNote" type="button" style="display: none;">Borrar</button>
      <button class="btn newNote"  type="button"  style="display: none;">Nueva</button>
    </form>

  </div>
</div>


<script>
  $('.delFileNutri').on('click', function (e) {
      if (confirm('Eliminar el archivo?')) {
          $('#fileID').val($(this).data('k'));
          $('#delFiles').submit();
      }
  });
  $('.clearClinicHist').click(function (e) {
      e.preventDefault();
      var id = $(this).data('id');
      if (confirm('Limpiar los datos actuales de la Historia Clínica?')) {
          var data = {
              uID: id,
              _token: '{{csrf_token()}}'
          };
          var posting = $.post('/admin/clearClinicHist', data).done(function (data) {
              if (data == 'OK') {
                  location.reload();
              } else {
                  alert(data);
              }
          });
      }
  });
</script>
<style>
  .boxFile {
    box-shadow: 1px 1px 5px 2px #cdcdcd;
    padding: 7px 16px;
    border-radius: 8px;
  }
  input[type="file"] {
    display: none;
  }
  .custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
  }
</style>