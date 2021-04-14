<div class="row">
    <div class="block col-md-12 bg-white">
        <div class="block-1"><img src="http://evolutio.virtual/admin-css/assets/img/profile.png" class="img-responsive" style="max-width: 100%;"></div>
        <div class="block-2">
            <h2>{{$user->name}}</h2>
            <h4>{{$user->email}}</h4>
        </div>
        <div class="block-3">
            <select class="form-control" id="selectMonth">
                @foreach($aMonths as $k=>$v)
                <option value="{{$k}}" <?php echo ($month == $k) ? 'selected' : '' ?>>{{$v.' '.$year}}</option>
                @endforeach
            </select>
        </div>
        <div class="block-3">
            <button class="btn btn-success" id="sendLiquid">
                <i class="fa fa-envelope"></i> enviar
            </button>
        </div>
    </div>
</div>


<div class="row">
    <div class="block col-md-12 bg-white">
        <form class="form-horizontal" action="{{ url('/admin/usuarios/update') }}" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="id" value="{{ $user->id }}">
            <div class="col-md-12 col-xs-12 push-20">

                <div class="col-lg-4 col-md-4">
                    <div class="form-material">
                        <input class="form-control" type="text" id="name" name="name" required value="<?php echo $user->name ?>">
                        <label for="name">Nombre</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4  push-20">
                    <div class="form-material">
                        <input type="text" id="email" class="form-control" name="email" required value="<?php echo $user->email ?>">
                        <label for="email">E-mail</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4  push-20">
                    <div class="form-material">
                        <input class="form-control" type="number" id="telefono" name="telefono" required maxlength="9" value="<?php echo $user->telefono ?>">
                        <label for="telefono">Teléfono</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4  push-20">
                    <div class="form-material">
                        <input class="form-control" type="text" id="iban" name="iban" maxlength="20" value="<?php echo $user->iban ?>" >
                        <label for="iban">IBAN</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="form-material">
                        <input class="form-control" type="password" id="password" name="password" value="">
                        <label for="password">Contraseña</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="form-material">
                        <select class="form-control" id="role" name="role" style="width: 100%;" data-placeholder="Seleccione una role" required>
                            <option value="user" <?php if ($user->role == "user") echo "selected";?>>Usuario/Cliente</option>
                            <option value="admin"  <?php if ($user->role == "admin") echo "selected";?>>Administrador</option>
                            <option value="teach" <?php if ($user->role == "teach") echo "selected";?>>Entrenador</option>
                            <option value="fisio" <?php if ($user->role == "fisio") echo "selected";?>>Fisioterapia</option>
                            <option value="nutri" <?php if ($user->role == "nutri") echo "selected";?>>Nutricionista</option>
                        </select>
                        <label for="role">Role</label>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="form-material">
                        <input class="form-control" type="text" id="salario_base" name="salario_base" maxlength="20" value="{{$salario_base}}" >
                        <label for="salario_base">Salario Base</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="form-material">
                        <input class="form-control" type="text" id="ss" name="ss" maxlength="18" value="<?php echo $user->ss ?>">
                        <label for="ss">Seg. soc</label>
                    </div>
                </div>
                    
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="form-material">
                        <input class="form-control" type="text" id="ppc" name="ppc" maxlength="18" value="{{$ppc}}">
                        <label for="ppc">P.P.C</label>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 push-20">
                    <div class="col-xs-2 mx-1em">
                        <button class="btn btn-horarios" type="button" data-id="{{ $user->id }}">Horarios</button>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xs-12 push-20 text-center">
                <button class="btn btn-success" type="submit">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar
                </button>
            </div>
        </form>

    </div>
</div>

<div class="row">
    <div class="block col-md-12 bg-white">
        <div id="blockLiquid"></div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('#blockLiquid').load('/admin/liquidacion-Entrenador/{{$user->id}}');
    
    $('#selectMonth').on('change',function (event) {
        var val = $(this).val();
        $('#blockLiquid').load('/admin/liquidacion-Entrenador/{{$user->id}}/'+val);
        $('#sendLiquid').attr("disabled", false);
    });
    $('#blockLiquid').on('change','.liquidation',function (event) {
        var id_coach = {{$user->id}};
        var date = $(this).data('k');
        var importe = $(this).val();
        $.get('/admin/payment-Entrenador/', {id_coach: id_coach, importe: importe, date: date}).done(function (resp) {
          if (resp !== 'OK'){
              alert(resp);
          }
        });
      });
    $('#sendLiquid').on('click',function (event) {
        var id_coach = {{$user->id}};
        var date = $('#selectMonth option:selected').val();
        var dateText = $('#selectMonth option:selected').text();
        var that = $(this);
        $.get('/admin/enviar-liquidacion-Entrenador/'+id_coach+'/'+date).done(function (resp) {
          if (resp == 'OK'){
              alert('Liquidación '+dateText+' enviada');
              that.attr("disabled", true);
          } else alert(resp);
        });
      });  
      
});
    
</script>