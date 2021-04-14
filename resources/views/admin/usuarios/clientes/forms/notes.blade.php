<h3 class="text-left">ANOTACIONES</h3>
<div class="row">
    <div class="col-md-8 ">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <?php
                    if ($oNotes):
                        foreach ($oNotes as $v):
                            $dateTime = strtotime($v->created_at);
                            $type = '';
                            switch ($v->type) {
                                case 'nutri': $type = 'Nutrición';
                                    break;
                                case 'fisio': $type = 'Fisioterapeuta';
                                    break;
                                case 'pt': $type = 'Entr. Pers.';
                                    break;
                                default: $type = 'Otros';
                                    break;
                            }
                            $personal = isset($allCoachs[$v->id_coach]) ? $allCoachs[$v->id_coach] : '-';
                            ?>
                            <tr>
                                <th width="100px">{{convertDateToShow_text(date('Y-m-d',$dateTime),true)}}<br/>
                                    {{$type}}<br/>
                                    {{$personal}}<br/>
                                    <button class="btn editNote" data-id="{{$v->id}}" data-note="{{$v->note}}">Editar</button></th>
                                <td>{{$v->note}}</td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4 ">
        
   
        <form  action="{{ url('/admin/usuarios/notes') }}" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="uid" value="{{ $user->id }}">
            <input type="hidden" name="id" id="noteID" value="">
            <div class="form-material">
                <label for="name">Nota</label>
                <textarea name="note" id="note" class="form-control" style="min-height: 50vh; border: 1px solid #cecece;padding: 9px;"></textarea>
            </div>
            <button class="btn btn-success" type="submit">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
            <button class="btn btn-danger" id="delNote" type="button" style="display: none;">Borrar</button>
            <button class="btn" id="newNote" type="button"  style="display: none;">Nueva</button>
        </form>
        
    </div>
</div>
