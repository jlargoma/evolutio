<h2 class="text-center"><?php echo ($id > 0) ? 'Editar Cita' : 'Nueva Cita' ?></h2>
<form action="{{ url('/admin/citas/create') }}" method="post" id="formEdit">
  @if($id>0)
  <input type="hidden" name="idDate" value="{{$id}}">
  @endif
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="date_type" id="date_type" value="{{$date_type}}">

  <div class="row">
    <div class="col-xs-12 col-md-4 push-20">
      @if($id<1) <label for="id_user" id="tit_user">
        <i class="fa fa-plus" id="newUser"></i>Cliente
        <span><input type="checkbox" id="is_group" name="is_group">Es un Grupo</span>
        </label>
        <div id="div_user">
          <select class="js-select2 form-control" id="id_user" name="id_user" style="width: 100%; cursor: pointer" data-placeholder="Seleccione usuario..">
            <option></option>
            <?php foreach ($users as $key => $user) : ?>

              <option value="<?php echo $user->id; ?>" <?php if (isset($id_user) && $id_user == $user->id) echo 'selected' ?>>
                <?php echo $user->name; ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>
        <input class="form-control" type="text" id="u_name" name="u_name" placeholder="Nombre del usuario" style="display:none" />
        @else
        <input type="hidden" name="id_user" id="id_user" value="{{$oUser->id}}">
        <label for="id_user" id="tit_user">Cliente</label>
        <input class="form-control" value="{{$oUser->name}}" disabled="" />
        @endif


    </div>
    <div class="col-xs-12 col-md-4 push-20">
      <label for="id_email">Email</label>
      <input class="form-control" type="email" id="NC_email" name="email" placeholder="email" value="{{$email}}" />
    </div>
    <div class="col-xs-12 col-md-4 push-20">
      <label for="id_email">Teléfono</label>
      <input class="form-control" type="text" id="NC_phone" name="phone" placeholder="Teléfono" value="{{$phone}}" />
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4 col-md-2  push-20">
      <label for="date">Fecha</label>
      <input class="js-datepicker form-control" value="{{$date}}" type="text" id="date" name="date" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy" />
    </div>
    <div class="col-xs-3 col-md-1 not-padding  push-20">
      <label for="id_user">hora</label>
      <select class="form-control" id="hour" name="hour" style="width: 100%;" data-placeholder="hora" required>
        <?php for ($i = 8; $i <= 22; $i++) : ?>
          <?php
          if ($i < 10) {
            $hour = "0" . $i;
          } else {
            $hour = $i;
          }
          ?>
          <option value="<?php echo $hour ?>" <?php if ($time == $i) echo 'selected'; ?>>
            <?php echo $hour ?>: 00
          </option>
        <?php endfor; ?>

      </select>
    </div>
    <div class="col-xs-4 col-md-1  push-20">
      <label for="date">Hora Exacta</label>
      <input class="form-control" type="time" value="{{$customTime}}" type="text" id="customTime" name="customTime">
    </div>
    <div class="col-xs-6 col-md-2 push-20">
      <label for="id_coach">{{$date_type_u}}</label>
      <select class="js-select2-coach form-control" id="id_coach" name="id_coach" style="width: 100%; cursor: pointer" data-placeholder="Seleccione coach..">
        <option></option>
        <?php foreach ($coachs as $key => $coach) : ?>
          <option value="<?php echo $coach->id; ?>" <?php if (isset($id_coach) && $id_coach == $coach->id) echo 'selected' ?>>
            <?php echo $coach->name; ?>
          </option>
        <?php endforeach ?>
      </select>

    </div>
    <div class="col-xs-6 col-md-4 push-20">
      <label for="id_type_rate">Servicio</label>
      <select class="js-select2 form-control" id="id_rate" name="id_rate" style="width: 100%;" data-placeholder="Seleccione un servicio" required>
        <option></option>
        <?php foreach ($services as $key => $service) : ?>
          <option value="<?php echo $service->id; ?>" data-price="<?php echo $service->price ?>" <?php if (isset($id_serv) && $id_serv == $service->id) echo 'selected' ?>>
            <?php echo $service->name; ?>
          </option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-xs-6 col-md-2 push-20">
      <label for="importeFinal">Precio</label>
      <input id="importeFinal" type="number" step="0.01" name="importe" class="form-control" value="{{$price}}">
    </div>

  </div>

  <div class="row">
    <div class="col-xs-4 col-md-2  push-20">
      <label for="id_type_rate">Sala</label>
      <select class="form-control" id="id_room" name="id_room" data-placeholder="Seleccione una sala" required>
        <option>Sin Sala</option>
        <?php for ($i = 1; $i < 7; $i++) : ?>
          <option value="<?php echo $i; ?>" <?php if (isset($id_room) && $id_room == $i) echo 'selected' ?>>
            <?php echo $i; ?>
          </option>
        <?php endfor ?>
      </select>
    </div>

    <div class="col-xs-8 col-md-7  push-20 block-icons-form">

      @if($date_type=='fisio' )
      <div class=" checkbox_ecogr <?php echo (isset($ecogr) && $ecogr == 1) ? "active" : ''; ?>">
        <input type="checkbox" id="equipments[]" name="equipments[]" value="ecogr" <?php echo (isset($ecogr) && $ecogr == 1) ? "checked" : ''; ?>>
        <img src="/img/ecog-gris.png" class="grey" alt="ecografo">
      </div>
      <div class=" checkbox_indiba <?php echo (isset($indiba) && $indiba == 1) ? "active" : ''; ?>">
        <input type="checkbox" id="equipments[]" name="equipments[]" value="indiba" <?php echo (isset($indiba) && $indiba == 1) ? "checked" : ''; ?>>
        <img src="/img/indiba-gris.png" class="grey" alt="Sin indiba">
      </div>
      @endif

      @if($date_type=='esthetic' )
      <div class=" checkbox_equip_a  <?php echo (isset($equip_a) && $equip_a == 1) ? "active" : ''; ?>">
        <input type="checkbox" id="equipments[]" name="equipments[]" value="equip_a" <?php echo (isset($equip_a) && $equip_a == 1) ? "checked" : ''; ?>>
        <img src="/img/maq-estetica-a-gris.png" class="grey" alt="">
      </div>
      <div class=" checkbox_equip_b  <?php echo (isset($equip_b) && $equip_b == 1) ? "active" : ''; ?>">
        <input type="checkbox" id="equipments[]" name="equipments[]" value="equip_b" <?php echo (isset($equip_b) && $equip_b == 1) ? "checked" : ''; ?>>
        <img src="/img/maq-estetica-b-gris.png" class="grey" alt="">
      </div>
      <div class=" checkbox_equip_c  <?php echo (isset($equip_c) && $equip_c == 1) ? "active" : ''; ?>">
        <input type="checkbox" id="equipments[]" name="equipments[]" value="equip_c" <?php echo (isset($equip_c) && $equip_c == 1) ? "checked" : ''; ?>>
        <img src="/img/maq-estetica-c-gris.png" class="grey" alt="">
      </div>
      @endif
    </div>
    <div class="col-xs-12 col-md-3  push-20 ">
      <label for="id_type_rate" class="btnExtr">+ Servicios <i class="fa fa-pencil"></i></label>
      <input type="hidden" name="extrIDs" id="extrIDs" value="<?= $extrs?>">
      <ul class="lstextr"></ul>
    </div>
  </div>
  <div class="table-responsive tableExtrs" style="display:none">
    <table class="table">
      <thead>
        <tr>
          <th colspan="3" class="text-center">Editar Servicios Extras</th>
        </tr>
      </thead>
    <?php 
      $extrIDs = explode(',',$extrs);
      foreach ($services as $key => $service): 
        $extrAsign = in_array($service->id,$extrIDs);
      ?>
      <tr>
        <td><?php echo $service->name; ?></td>
        <td><?php echo moneda($service->price); ?></td>
        <td>
          <button type="button" 
          class="btn btn-<?= $extrAsign ? 'danger' : 'success'?> editExtrs" 
          data-k="<?= $service->id;?>"
          data-price="<?= $service->price;?>"
          data-name="<?= $service->name;?>"
          ><?= $extrAsign ? 'X' : '+'?></button></td>
      </tr>
      <?php endforeach ?>
    </table>
  </div>
</form>

<div class=" row">
  <div class="col-xs-12 text-center">
    @if($id>0)
    <button class="btn btn-lg btn-user" type="button" data-idUser="{{$id_user}}">
      Ficha Usuario
    </button>
    @endif
    <button class="btn btn-lg btn-success sendForm" data-id="formEdit" type="button">
      Guardar
    </button>
    @if($id>0)
    <button class="btn btn-lg btn-danger btnDeleteCita" type="button">
      Eliminar
    </button>
    <a href="/admin/citas/duplicar/{{$id}}" class="btn btn-lg btn-secondary">
      Duplicar
    </a>





    @endif

  </div>
  @if($id>0 && $date_type == 'nutri')
  <div class="col-xs-12 text-center">
    @if(isset($encNutr))
    <a href="/admin/ver-encuesta/{{$btnEncuesta}}" class="btn btn-lg btn-info" target="_black">
      Ver encuesta
    </a>
    <button class="btn btn-lg btn-success clearEncuesta" data-id="{{$id_user}}" type="button">
      Vaciar encuesta
    </button>
    @else
    <button class="btn btn-lg btn-success sendEncuesta" data-id="{{$id_user}}" type="button">
      Reenviar encuesta
    </button>
    @endif
  </div>
  @endif

</div>

@if(!$charge && $id>0)
<div class="row">
  @include('calendars.cobrar')
</div>
@endif
@if($id>0 && $charge)
<div class="tpayData ">
  <table class="table">
    <tr>
      <td colspan="2" class="success">Cobrado</td>
    </tr>
    <tr>
      <th>Fecha:</th>
      <td>{{dateMin($charge->date_payment)}}</td>
    </tr>
    <tr>
      <th>Desc.:</th>
      <td>{{$charge->discount}}%</td>
    </tr>
    <tr>
      <th>Precio:</th>
      <td>{{moneda($charge->import)}}</td>
    </tr>
    <tr>
      <th>Metodo:</th>
      <td>{{payMethod($charge->type_payment)}}</td>
    </tr>
  </table>
</div>
@endif


<style>
  /* .block-icons-form{
      clear: both;
      overflow: auto;
    } */
  .block-icons-form div {
    float: left;
    margin: 22px;
    padding: 7px 13px;
    height: 54px;
    overflow: hidden;
    box-shadow: 1px 1px 5px #858585;
    border-radius: 7px;
  }

  .block-icons-form div input {
    margin-right: 7px;
  }

  .block-icons-form div img {
    max-width: 39px;
    margin-right: 2em;
    max-height: 100%;
  }

  .block-icons-form div.active {
    border: 3px solid #5c90d2;
  }

  .block-icons-form .disable {
    background-color: #f9a6a6;
  }


  select option:disabled,
  select:invalid {
    color: #ff9898;
    font-weight: bold;
  }
  label.btnExtr {
  cursor: pointer;
  background-color: #46c37b;
    display: block;
    padding: 5px;
    color: #FFF;
}
label.btnExtr:hover {
  background-color: #128944;
}
.table-responsive.tableExtrs {
  max-width: 500px;
  margin: 1em auto;
  box-shadow: 1px 1px 4px #000;
}
ul.lstextr{
  padding: 0px;
}
ul.lstextr li {
  font-weight: 800;
  list-style: "+ ";
}
</style>

<script type="text/javascript">
  jQuery(function() {

    $('body').on('change', '#date,#hour', function() {
      window.checkAvail();
    });


    window.availCoach = [];

    window.checkAvail = function() {
      var data = {
        id: <?= $id ?>,
        date: $('#date').val(),
        time: $('#hour').val(),
        type: $('#date_type').val(),
        _token: '{{csrf_token()}}',
      };
      $.post('/admin/citas/checkDispCoaches', data, function(resp) {
        const aCoachs = resp.aCoachs;
        for (cID in aCoachs) {
          if (aCoachs[cID] == 0 || aCoachs[cID] == 1) {
            window.availCoach[cID] = '';
          } else {
            window.availCoach[cID] = 's_disable';
          }
        }

        const aRooms = resp.room;
        $('#id_room option').attr("disabled", false);
        for (rID in aRooms) {
          $('#id_room option[value="' + aRooms[rID] + '"]').attr("disabled", true);
        }


        const equip = ['ecogr', 'indiba', 'equip_a', 'equip_b', 'equip_c'];
        for (i in equip) {
          if (resp[equip[i]] > 0) $('.checkbox_' + equip[i]).addClass('disable');
          else $('.checkbox_' + equip[i]).removeClass('disable');
        }



      });
    }
    window.checkAvail();



    var lstextr = function(){
      let items = '';
      let ids = [];
      $('.editExtrs').each(function(){
        if ($(this).hasClass('btn-danger')){
          items += '<li>'+$(this).data('name')+'</li>';
          ids.push($(this).data('k'));
        }
      });

      $('#extrIDs').val(ids.join(','));
      $('.lstextr').html(items);
    }
    
    lstextr();
    $('.btnExtr').on('click', function(){
       $('.tableExtrs').toggle();
    });
    $('.editExtrs').on('click', function(){
      let importeFinal = parseInt($('#importeFinal').val());
      if ($(this).hasClass('btn-danger')){
        importeFinal = importeFinal - parseInt($(this).data('price'));
        $(this).removeClass('btn-danger').addClass('btn-success');
      } else {
        importeFinal = importeFinal + parseInt($(this).data('price'));
        $(this).removeClass('btn-success').addClass('btn-danger');
      }
      $('#importeFinal').val(importeFinal);
      lstextr();
    });

    $("#id_rate").change(function () {
      $('#extrIDs').val('');
      $('.lstextr').html('');
      $('.editExtrs.btn-danger').removeClass('btn-danger').addClass('btn-success');
    });
    

  });
</script>