<h3 class="text-left">SERVICIOS ASOCIADOS</h3>
<div class="table-responsive">
    <table class="table">
        @if($uCurrentRates)
        @foreach($uCurrentRates as $r)
        <tr>
            <td>{{$r->name}}</td>
            <td>{{$r->price}}€</td>
            <td>
                <a 
                    href="/admin/clientes-unassigned/{{ $user->id }}/{{$r->id}}/{{date('Y-m')}}"
                    onclick="return confirm('Remover el servicio para el periodo en curso?')"
                    >
                    <i class="fa fa-trash "></i>
                </a>
            </td>
        </tr>
        @endforeach
        @endif
    </table>
</div>
<div class="row">
<form action="/admin/add-service" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="{{ $user->id }}">
    <div class="col-md-6  push-20">
        <div class="form-material">
            <select class="form-control" id="id_rate" name="id_rate" style="width: 100%; cursor: pointer" data-placeholder="Seleccione tarifas.." >
                <option></option>
                <?php foreach ($aRates as $rate): ?>
                    <?php
                    if ($rate->status == 1): $class = "green";
                    else: $class = "blue";
                    endif
                    ?>
                    <option value="<?php echo $rate->id ?>" class="<?php echo $class; ?>">
                    <?php echo $rate->name ?>
                    </option>
                <?php endforeach ?>
            </select>
            <label for="id_rate">Agregar Servicio</label>
        </div>
    </div>
    <div class="col-md-6  push-20">
        <button class="btn btn-success">Agregar</button>
    </div>
</form>
    </div>