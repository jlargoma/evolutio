<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">
<div class="col-xs-12 bg-white">
	<div class="row">
		<div class="col-xs-12 push-20">
			<h2 class="text-center">Nuevo Gasto</h2>
		</div>
		<div class="col-xs-12">
			<form action="{{ url('/admin/gastos/create') }}" method="post">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id_empresa" value="1">
				<div class="col-xs-12 col-md-1 push-20">
					<label for="date">fecha</label>
					<input type="text" class="js-datepicker form-control" name="date" data-date-format="dd-mm-yyyy" placeholder="Fecha" value="<?php echo date('d-m-Y') ?>" />
					
				</div>
				<div class=" col-xs-12 col-md-3 push-20">
					<div class="col-xs-12 col-md-12 push-20">
						<label for="concept">Concepto</label>
						<input  type="text" class="form-control" name="concept"/>
					</div>
				</div>

				<div class="col-xs-12 col-md-2 push-20">
					<label for="type">Tipo de Gasto</label>
					<select class="js-select2 form-control" id="type" name="type" style="width: 100%;" data-placeholder="Seleccione un tipo" required >
		                <option></option>
		                <option value="MOBILIARIO">MOBILIARIO</option>
		                <option value="SERVICIOS PROFESIONALES INDEPENDIENTES">SERVICIOS PROFESIONALES INDEPENDIENTES</option>
		                <option value="VARIOS">VARIOS</option>
		                <option value="EQUIPAMIENTO DEPORTIVO">EQUIPAMIENTO DEPORTIVO</option>
		                <option value="IMPUESTOS">IMPUESTOS</option>
		                <option value="SUMINISTROS">SUMINISTROS</option>
		                <option value="GASTOS BANCARIOS">GASTOS BANCARIOS</option>
		                <option value="PUBLICIDAD">PUBLICIDAD</option>
		                <option value="REPARACION Y CONSERVACION">REPARACION Y CONSERVACION</option>
		                <option value="ALQUILER NAVE">ALQUILER NAVE</option>
		                <option value="SEGUROS SOCIALES">SEGUROS SOCIALES</option>
		                <option value="NOMINAS">NOMINAS</option>
		                <option value="TARJETA VISA">TARJETA VISA</option>
		                <option value="MATERIAL OFICINA">MATERIAL OFICINA</option>
		                <option value="MENSAJERIA">MENSAJERIA</option>
		                <option value="PRODUCTOS VENDING">PRODUCTOS VENDING</option>
		                <option value="LIMPIEZA">LIMPIEZA</option>
		                <option value="INTERNET">INTERNET</option>
		                <option value="RENTING EQUIPAMIENTO DEPORTIVO">RENTING EQUIPAMIENTO DEPORTIVO</option>
		                <option value="COMISONES COMERCIALES">COMISONES COMERCIALES</option>
		            </select>
				</div>

				<div class="col-xs-12 col-md-2 push-20">
					<label for="import">Importe</label>
					<input  type="text" name="import" id="import" class="form-control" />

				</div>
				<div class="col-xs-12 col-md-2 push-20">
					<label for="pay_for">Pagada por</label>
					<select class="js-select2 form-control" id="pay_for" name="pay_for" style="width: 100%;" data-placeholder="Seleccione una" required>
		                <option></option>
		                <option value="BANCO"> BANCO </option>
		                <option value="CAJA"> CAJA </option>
		                <option value="APORTACIÓN JORGE"> APORTACIÓN JORGE </option>
		                <option value="APORTACIÓN VICTOR"> APORTACIÓN  VICTOR</option>
		                <option value="APORTACIÓN ALEX"> APORTACIÓN ALEX </option>
		            </select>

				</div>
			
				<div class="col-xs-12 col-md-2 push-20">
					<div class="col-xs-12">
						<label for="type_payment">Gasto/Inversión</label>
					</div>
					<div class="col-xs-6">
						<label class="css-input css-radio css-radio-lg css-radio-primary push-10-r">
							<input type="radio" name="type_payment" checked="" value="GASTO"><span></span> GASTO
						</label>
					</div>
					<div class="col-xs-6">
						<label class="css-input css-radio css-radio-lg css-radio-primary">
							<input type="radio" name="type_payment" value="INVERSIÓN"><span></span> INVERSIÓN
						</label>
					</div>
				</div>

				<div class=" col-xs-12 form-group push-20">
					<div class="col-xs-12 col-md-12 push-20">
						<label for="comment">Observaciones</label>
						<textarea class="form-control" name="comment"></textarea>
					</div>
				</div>
				<div class=" col-xs-12 form-group text-center push-20">
					<button class="btn btn-lg btn-success">Añadir</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script type="text/javascript">
	jQuery(function () {
        App.initHelpers(['datepicker', 'select2']);
    });
    $(document).ready(function() {
    
    });
</script>