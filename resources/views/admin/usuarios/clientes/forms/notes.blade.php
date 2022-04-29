
<?php
$csrf_token = csrf_token();
?>

<div class="row">
  <div class="col-md-8">
    <div class="boxFile">
      <h3 class="text-left">ARCHIVOS DE ENTR.PERSONAL</h3>
      <form enctype="multipart/form-data" action="{{ url('/admin/clientes/saveFiles') }}" method="post">
        <input type="hidden" name="_token" value="{{ $csrf_token }}">
        <input type="hidden" name="uid" value="{{ $user->id }}">
        <input type="hidden" name="type" value="pt">
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <input type="text" name="fileName" class="form-control" placeholder="Nombre dell archivo" required="">
            </div>
            <div class="col-md-3">
              <label class="custom-file-upload">
                <input type="file" name="file"/>
                <i class="fa fa-cloud-upload"></i> Buscar Archivo
              </label>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </div>
        </div>
      </form>


      @if(isset($filesPT) && count($filesPT) )
      <form action="{{ url('/admin/clientes/delFiles') }}" method="post" id="delFiles">
        <input type="hidden" name="_token" value="{{ $csrf_token }}">
        <input type="hidden" name="uid" value="{{ $user->id }}">
        <input type="hidden" name="fid" id="fileID">
      </form>
      <table class="table">
        @foreach($filesPT as $k=>$v)
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
</div>







<hr class="line">
<h3 class="text-left">ANOTACIONES</h3>
<div class="row blockNote">
  <div class="col-md-8 col-xs-12 ">
    <?php
    if ($oNotes):
      foreach ($oNotes as $v):
        if ($v->type == 'gral') {
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
      <!--<input type="hidden" name="type" value="gral">-->
      <div class="form-simple">
        <label for="name">Usuario</label>
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
      <div class="form-simple">
        <label for="name">Tipo de nota</label>
        <select class="form-control" name="type" required>
          <option value="gral">Entr. Personal</option>
          <option value="nutri">Nutrición</option>
          <option value="fisio">Fisioterapia</option>
        </select>
      </div>
      <button class="btn btn-success" type="submit">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
      </button>
      <button class="btn btn-danger delNote" type="button" style="display: none;">Borrar</button>
      <button class="btn newNote"  type="button"  style="display: none;">Nueva</button>
    </form>

  </div>
</div>
