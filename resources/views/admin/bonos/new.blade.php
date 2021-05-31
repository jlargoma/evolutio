<div class="row push-30">
  <div class="block bg-white">
      <h3 class="text-center">
        Formulario para añadir Bonoss
      </h3>
    <form class="form-horizontal" action="{{ url('/admin/bonos/create') }}" method="post">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <div class="row  mx-1em">
        <div class="col-md-6 mx-1em">
          <div class="form-material">
            <input class="form-control" type="text" id="name" name="name" required>
            <label for="nombre">Nombre del Bono</label>
          </div>
        </div>
        <div class="col-md-6 mx-1em">
          <div class="form-material">
            <input class="form-control only-numbres" type="text" id="price" name="price" required>
            <label for="nombre">Precio</label>
          </div>
        </div>
        <div class="col-md-6 mx-1em">
          <div class="form-material">
            <input class="form-control" type="number" id="qty" name="qty" required>
            <label for="nombre">Cantidad de bonos</label>
          </div>
        </div>
        <div class="col-md-6 mx-1em">
          <div class="form-material">
            <input class="form-control" type="number" id="value" name="value" required>
            <label for="nombre">Valor de cada bono</label>
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
</div>
