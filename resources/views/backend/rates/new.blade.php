<form class="form-horizontal" action="{{ route('tarifas.nueva') }}" method="post">
  <div class="modal-body">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="row">
      <div class="col-md-6 mx-1em">
        <div class="form-material">
          <input class="form-control" type="text" id="name" name="name" required>
          <label for="nombre">Nombre de Tarifa</label>
        </div>
      </div>
      <div class="col-md-6 mx-1em">
        <div class="form-material">
          <select class="js-select2 form-control" id="type" name="type" style="width: 100%;" data-placeholder="Tipo de tarifa..." required>
            <option></option>
            <?php foreach ($services as $service): ?>
              <option value="{{$service->id}}" <?php if ($service->id == $obj->type) echo "selected"; ?>>
                {{$service->name}}
              </option>
            <?php endforeach ?>
          </select>
          <label for="name">Bloque Asignado</label>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 mx-1em">
        <div class="form-material">
          <select class="js-select2 form-control" id="mode" name="mode" style="width: 100%;" data-placeholder="Tipo de tarifa..." required>
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?php echo $i ?>">
                <?php echo $i ?>
                <?php if ($i > 2): ?>
                  Meses
                <?php else: ?>
                  Mes
                <?php endif ?>
              </option>
            <?php endfor ?>
          </select>
          <label for="mode">Modo Mensualidad</label>
        </div>
      </div>
      <div class="col-md-3 mx-1em">
        <div class="form-material">
          <select class="form-control" id="status" name="status" style="width: 100%;" data-placeholder="Tipo de tarifa..." required>
            <option value="1">Nueva</option>
            <option value="0">Vieja</option>
          </select>
          <label for="status">Estado</label>
        </div>
      </div>
      <div class="col-md-3 mx-1em">
        <div class="form-material">
          <input class="form-control" type="number" id="max_pax" name="max_pax">
          <label for="nombre">N. max clases</label>
        </div>
      </div>
    </div>
    <div class="row mx-1em">
      <div class="col-md-6">
        <div class="form-material">
          <select class="form-control" id="tarifa" name="tarifa" data-placeholder="Tipo de tarifa...">
            <option value=""></option>
            <?php
            $lstCodes = rates_codes();
            foreach ($lstCodes as $item):
              ?>
              <option value="{{$item}}">{{$item}}</option>
              <?php
            endforeach;
            ?>
          </select>
          <label for="nombre">Tipo de Tarifa</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-material">
          <input class="form-control only-numbres" type="text" id="price" name="price" required>
          <label for="nombre">Precio</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-material">
          <input class="form-control only-numbres" type="text" id="cost" name="cost" required>
          <label for="nombre">Costo / Sesión</label>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary" type="submit">Guardar</button>
  </div>
</form>