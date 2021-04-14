<form action="{{ url('/admin/citas/chargeAdvanced') }}" method="post" id="chargeDate">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="idDate" value="<?php echo $id ?>">
    <input type="hidden" name="id_rate" value="<?php echo $id_serv ?>">
    <div class="col-xs-12 col-md-6 push-20">
        <label for="type">Tipo de cobro</label>
        <select class=" form-control" id="type" name="type" style="width: 100%;" placeholder="Seleccione acción" required >
            <option value="3">Efectivo</option>
            <option value="4">Tarjeta</option>
            <option value="5">Invitado</option>
        </select>
    </div>
    <div class="col-xs-12 col-md-6 push-20">
        <label for="">Precio</label>
        <input type="number" class="only-numbers form-control" id="priceRate" name="importe" value="{{$price}}">
    </div>
</form>

