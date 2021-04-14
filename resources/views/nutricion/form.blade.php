<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">

<div class="col-xs-12 bg-white">
    <h2 class="text-center"><?php echo ($id > 0) ? 'Editar Cita' : 'Nueva Cita' ?></h2>
    <div class="row">
        <form action="{{ url('/admin/citas/create') }}" method="post" id="formEdit">
            @if($id>0)            			
            <input type="hidden" name="idDate" value="{{$id}}">
            @endif
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="date_type" value="nutri">
            <div class="col-xs-12 form-group push-20">
                <div class="col-xs-12 col-md-6 push-20">
                    <label for="id_user">
                        @if($id<1) 
                        <i class="fa fa-plus" id="newUser"></i>
                        @endif
                        Cliente</label>
                    <select class="js-select2 form-control" id="id_user" name="id_user" style="width: 100%; cursor: pointer" data-placeholder="Seleccione usuario.."  >
                        <option></option>
                        <?php foreach ($users as $key => $user): ?>

                            <option value="<?php echo $user->id; ?>" <?php if (isset($id_user) && $id_user == $user->id) echo 'selected' ?>>
                                <?php echo $user->name; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-xs-12 col-md-6 push-20">
                    <label for="id_email">Email</label>
                    <input class="form-control" type="email" id="NC_email" name="email" placeholder="email" value="{{$email}}"/>
                </div>
            </div>
            <div class=" col-xs-12 form-group push-20">
                <div class="col-xs-12 col-md-3  push-20">
                    <label for="date">Fecha</label>
                    <input class="js-datepicker form-control" value="{{$date}}" type="text" id="date" name="date" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy"/>
                </div>
                <div class="col-xs-12 col-md-2 not-padding  push-20">
                    <label for="id_user">hora</label>
                    <select class="form-control" id="hour" name="hour" style="width: 100%;" data-placeholder="hora" required >
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
                <div class="col-xs-12 col-md-3 push-20">
                    <label for="id_coach">Nutricionista</label>

                    <select class="js-select2 form-control" id="id_coach" name="id_coach" style="width: 100%; cursor: pointer" data-placeholder="Seleccione coach.." >
                        <option></option>
                        <?php foreach ($coachs as $key => $coach): ?>
                            <option value="<?php echo $coach->id; ?>" <?php if (isset($id_coach) && $id_coach == $coach->id) echo 'selected' ?>>
                                <?php echo $coach->name; ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                </div>
                <div class="col-xs-12 col-md-4 push-20">
                    <label for="id_type_rate">Servicio</label>
                    <select class="js-select2 form-control" id="id_rate" name="id_rate" style="width: 100%;" data-placeholder="Seleccione un servicio" required >
                        <option></option>
                        <?php foreach ($services as $key => $service): ?>
                            <option value="<?php echo $service->id; ?>" data-price="<?php echo $service->price ?>" <?php if (isset($id_serv) && $id_serv == $service->id) echo 'selected' ?>>
                                <?php echo $service->name; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </form>
        <div class="col-xs-12 form-group push-20">
            @if($charged != 1)
            @include('nutricion.cobrar')
            @endif
        </div>
        <div class=" col-xs-12 form-group push-20">
            <div class="col-xs-12 text-center">
                @if($id>0)   
                <button class="btn btn-lg btn-user" data-toggle="modal" data-target="#modalCliente" type="button" data-idUser="{{$id_user}}">
                    Ficha Usuario
                </button>
                @endif
                <button class="btn btn-lg btn-success sendForm" data-id="formEdit"  type="button" >
                    Guardar
                </button>
                @if($id>0)   
                <button class="btn btn-lg btn-danger btnDeleteCita" type="button">
                    Eliminar
                </button>
                @if($charged != 1)
                <button class="btn btn-lg btn-success sendForm" type="button" data-id="chargeDate">
                    Cobrar
                </button>	
                @endif
                @endif
            </div>
        </div>

        <hr/>
    </div>
    <div class="col-xs-12">
        @if($id>0)
        @if($charged == 1)
        <div class="alert alert-success">Cobrado</div>
        @endif
        @endif
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(function () {
        App.initHelpers(['datepicker', 'select2', 'datetimepicker']);
        $("#id_user").change(function() {
            var id = $(this).val();
            $.get('/admin/get-mail/' + id, function(data) {
                $('#NC_email').val(data);
            });
        });
        
        $('.sendForm').on('click', function(){
            $('#'+$(this).data('id')).submit();
        });
        $("#id_rate").change(function () {
            var price = $(this).find(':selected').data('price');
            $('#priceRate').val(price);
        });
        @if ($id > 0)
            $(".btnDeleteCita").click(function () {
                if (confirm('Eliminar la Cita?'))
                    location.assign("/admin/citas/delete/{{$id}}");
            });


        @else
            $('#newUser').click(function (e) {
                e.preventDefault();
                $('#content-new-user').empty().load('/admin/usuarios/new');
                $('#modal-add-date').modal('hide');
                $('#modal_newUser').modal();
            });
        @endif

        $('.btn-user').click(function (e) {
           e.preventDefault();
           var id = $(this).attr('data-idUser');
           $('#ifrCliente').attr('src','/admin/usuarios/informe/' + id);

        });
    });

</script>