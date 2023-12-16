<div class="row push-30">
    <div class="block bg-white">
        <h3 class="text-center">
            Formulario para añadir Convenios
        </h3>
        <form class="form-horizontal" action="{{ url('/admin/convenios/new') }}" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="row  mx-1em">
                <div class="col-md-12 mx-1em">
                    <div class="form-material">
                        <input class="form-control" type="text" id="name" name="name" required>
                        <label for="nombre">Nombre del Convenio</label>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <button class="btn btn-success" type="submit">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                </button>
            </div>
        </form>
    </div>
    <hr>
    <div class="block bg-white">
        <h3 class="text-center">
            Eliminar Convenio
        </h3>
        <form class="form-horizontal" action="{{ url('/admin/convenios/delete') }}" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="row  mx-1em">
                <div class="col-md-12 mx-1em">
                    <div class="form-material">
                        <label for="nombre">Nombre del Convenio</label>
                        <select class="form-control" id="convenio" name="convenio">
                            <option value="">Seleccionar</option>
                            @foreach($lstObjs as $oItem)
                            <option value="{{$oItem->id}}">{{$oItem->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <button class="btn btn-danger" type="submit">
                    <i class="fa fa-times" aria-hidden="true"></i> Borrar
                </button>
            </div>
        </form>
    </div>
</div>