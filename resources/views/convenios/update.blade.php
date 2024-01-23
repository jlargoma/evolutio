<div class="row push-30">
    <div class="block bg-white">
        <h3 class="text-center">
            Formulario para añadir Convenios
        </h3>
        <form class="form-horizontal" action="{{ url('/admin/convenios/update') }}" method="post">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <div class="row  mx-1em">
                <div class="col-md-8 mx-1em">
                    <div class="form-material">
                        <input class="form-control" type="text" id="convenio-name-update" name="name" required>
                        <label for="convenio-name-update">Nombre del Convenio</label>
                    </div>
                </div>
                <div class="col-md-4 mx-1em">
                    <div class="form-material">
                        <label for="cconvenio-omision-update">Comisión</label>
                        <input id="convenio-comision-update" oninput="limitDecimals(this, 2)" type="number" step="0.01" name="comision" class="form-control" value="0">
                    </div>
                </div>
            </div>
            <input type="hidden" id="convenio-id-update" name="id" value="" />
            <div class="row text-center">
                <button class="btn btn-success" type="submit">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<script>
     function limitDecimals(inputElement, decimalPlaces) {
      let inputValue = inputElement.value.toString();

      if (inputValue.includes('.') && inputValue.split('.')[1].length > decimalPlaces) {
        inputElement.value = parseFloat(inputValue).toFixed(decimalPlaces);
      }
    }
</script>