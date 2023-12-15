<div class="modal fade in" id="infoCita" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
            </li>
          </ul>
        </div>
        <div class="row block-content" id="contentInfoCita">

        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal  fade in" id="modalIfrm" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog lg modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button" class="reload"><i class="si si-close "> Cerrar y refrescar</i></button>
            </li>
          </ul>
        </div>
        <div><iframe id="ifrModal"></iframe></div>
      </div>
    </div>
  </div>
</div>
<div class="modal  fade in" id="modalCliente" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog lg modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button" class="reload"><i class="si si-close "> Cerrar y refrescar</i></button>
            </li>
          </ul>
        </div>
        <div><iframe id="ifrCliente"></iframe></div>
      </div>
    </div>
  </div>
</div>


<div class="modal  fade in" id="modalMultGroup" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog lg modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button" class="reload"><i class="si si-close "> Cerrar y refrescar</i></button>
            </li>
          </ul>
        </div>
        <div class="row content">


          <div class="col-xs-12">
            <h2 class="text-center">Crear Clases de la semana</h2>
            <div class="row">
              <form action="{{ url('/admin/citas/createMultip') }}" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="date_type" value="pt">
                <div class="row">
                  <div class="col-xs-12 col-md-6 push-20">
                      <table class="table   table-bordered table-header-bg">
                      <thead>
                        <tr>
                          <th>Fechas</th>
                          <th>Horarios (separados por coma)</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php for ($i = 0; $i < 6; $i++) : ?>
                          <tr>
                            <td><input class="form-control" type="date" name="dates[]" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy" /></td>
                            <td><input class="form-control" type="text" type="text" name="times[]"></td>
                          </tr>
                        <?php endfor; ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-xs-12 col-md-6 push-20">
                    <div class="mt-2"></div>
                    <label for="id_coach">Entrenador</label>
                    <select class="js-select2-coach form-control" id="id_coach" name="id_coach" style="width: 100%; cursor: pointer" data-placeholder="Seleccione coach..">
                      <option></option>
                      <?php foreach ($coachs as $key => $coach) : ?>
                        <option value="<?php echo $coach->id; ?>">
                          <?php echo $coach->name; ?>
                        </option>
                      <?php endforeach ?>
                    </select>


                    <label for="id_type_rate">Servicio</label>
                    <select class="form-control" id="id_rate" name="id_rate" style="width: 100%;" data-placeholder="Seleccione un servicio" required>
                      <option value="-1"></option>
                      <?php foreach ($services as $key => $service) : if ($service->status == 0 || $service->show_list < 1) continue; ?>
                        <option value="<?php echo $service->id; ?>" <?php if (isset($id_serv) && $id_serv == $service->id) echo 'selected' ?>>
                          <?php echo $service->name; ?>
                        </option>
                      <?php endforeach ?>
                    </select>
                    <button class="btn btn-lg mt-2 btn-success">
                      Guardar
                    </button>
                  </div>

                </div>
              </form>


            </div>
          </div>













        </div>
      </div>
    </div>
  </div>
</div>