<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
<?php use \Carbon\Carbon; ?>
@extends('layouts.admin-master')

@section('title') Lista de Gastos - <?php echo $date->copy()->format('Y') ?> @endsection

@section('content')
<style type="text/css">
	.header-navbar-fixed #main-container {
	    padding-top: 10px;
	}
</style>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.css') }}">
<div class="bg-white push-20">
	<section class="content content-full">
		<div class="row">
	 		<div class="col-md-12 col-xs-12 push-20">
	 			
	 			@include('admin.contabilidad._button-contabiliad')
	 			
	 		</div>

	 		<div style="clear: both;"></div>

			<div class="col-xs-12 col-md-4">
				<div class="col-xs-12 not-padding">
					<canvas id="barChart" ></canvas>
				</div>
			</div>
			<div class="col-xs-12 col-md-2">
				<div class="col-xs-12 not-padding push-20">
					<canvas id="pieChart" ></canvas>
				</div>
			</div>
			<div class="col-xs-12 col-md-6">
				<div class="col-xs-12 col-md-6 ">
					<div class="block block-link-hover3 text-center push-10">
						<div class="block-content block-content-full bg-primary">
							<div class="h1 font-w700 text-white"> <?php echo number_format(abs($total),2,',','.') ; ?><span class="h2 text-white-op">€</span></div>
							<div class="h5 text-white-op text-uppercase push-5-t">Gastos anuales</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-6 ">
					<div class="block block-link-hover3 text-center push-10">
						<div class="block-content block-content-full bg-info">
							<?php $actualDate = Carbon::now(); ?>
							<?php $diff = $actualDate->diffInMonths($actualDate->copy()->startOfYear()); ?>
							<div class="h1 font-w700 text-white"> <?php echo number_format(abs($total/($diff+1)),2,',','.') ; ?><span class="h2 text-white-op">€</span></div>
							<div class="h5 text-white-op text-uppercase push-5-t">Media gasto/mes</div>
						</div>
					</div>
				</div>
				<?php $aux = $actualDate->copy()->subMonths(2) ?>
				<?php $countGastos = 0; ?>
				<?php for ($i=1; $i <= 3 ; $i++): ?>
					<?php
                        $data = DB::table("expenses")
							    	->select(DB::raw("SUM(import) as count"))
							    	->whereMonth('date','=' , $aux->copy()->format('m'))
                                	->whereYear('date','=' , $aux->copy()->format('Y'))
									->get();
						$expenses = $data[0]->count;

						if(  abs($expenses) > $countGastos){
							$countGastos = abs($expenses);
						} 
						if( $i > 1){
							if (abs($expenses) > $countGastos) {
								$statusExpenses = 1;
							}else{
								$statusExpenses = 0;
							}
						}
					?>
					<div class="col-xs-12 col-md-4 push-0">
						<div class="block block-link-hover3 text-center">
							<div class="block-content block-content-full  bg-gray-light">
								<div class="h3 font-w700 text-black"> 
									<?php echo number_format(abs($expenses),2,',','.') ; ?><span class="h2 text-black">€</span>
									<?php if ( isset($statusExpenses) && $statusExpenses == 1): ?>
										<i class="fa fa-arrow-up text-danger"></i>
									<?php elseif( isset($statusExpenses) && $statusExpenses == 0 ): ?>
										<i class="fa fa-arrow-down text-success"></i>
									<?php endif ?>  
								</div>
								<div class="h5 text-black-op text-uppercase push-5-t">
									Gastos <span class="font-w700"><?php echo ucfirst($aux->copy()->formatlocalized('%B')) ?></span>
								</div>
								
							</div>
						</div>
					</div>

					<?php $aux->addMonths(1) ?>
				<?php endfor; ?>
			</div>
		</div>
	</section>
</div>

<div class="bg-white">
	<section class="content content-full">
		<div class="row">
			<div class="col-xs-6 col-md-1 push-10">
				<button id="addCsv" class="btn btn-success" data-toggle="modal" data-target="#modal-csv" > 
					<i class="fa fa-plus "></i> Importar CSV
				</button>
			</div>
			<div class="col-xs-6 col-md-4 push-30">
				
				<div class="col-md-4">
					<select class="form-control" id="dateSelector">
						<?php $dateSelector = $date->copy()->subMonths($diffMonths); ?>
						<?php for ($i = 0;$i <= $diffMonths; $i++): ?>
							<?php if ($dateSelector->copy()->format('Y-m') == $selectedDate){ $selected = "selected";}else{ $selected = "";} ?>

							<option value="<?php echo $dateSelector->copy()->format('Y-m') ?>" <?php echo $selected ?>>
								<?php echo ucfirst($dateSelector->copy()->formatlocalized('%B')) ?>	de <?php echo $dateSelector->copy()->formatlocalized('%Y') ?>	
							</option>
							<?php $dateSelector->addMonths(1) ?>
						<?php endfor ?>

					</select>
				</div>
			</div>
		</div>
		<div class="row">
			
			<?php if (count($gastos) > 0): ?>
				<div class="col-xs-12 col-md-8">
					<h2 class="text-center font-w600 push-20">
						LISTADO DE GASTOS
						<?php if ($firstLoad): ?>
							<span class="font-w600"><?php echo $date->copy()->format('Y') ?></span>
						<?php else: ?>
							<span class="font-w600"><?php echo strtoupper($date->copy()->formatLocalized('%B %Y')) ?></span>
						<?php endif ?>
						
					</h2>
					<table class="table table-bordered table-striped table-header-bg no-footer">
						<thead>
							<tr>
								<!-- <th class="text-center">#</th> -->
								<th class="text-center" style="width: 100px;">Fecha</th>
								<th class="text-center">Concepto</th>
								<th class="text-center">Tipo</th>
								<th class="text-center">Gasto/Inver</th>
								<th class="text-center">Pagado por</th>
								<th class="text-center">importe</th>
								<th class="text-center">Observ.</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($gastos as $key => $gasto): ?>
								<tr>
									<!-- <td class="text-center">
										<?php echo $key+1; ?>
									</td> -->
									<td class="text-center" style="width: 100px;">
										<?php $date = Carbon::createFromFormat('Y-m-d', $gasto->date); ?>
										<b><?php echo strtoupper($date->formatLocalized('%d %b')); ?></b>
									</td>
									<td class="text-center">
										<?php echo $gasto->concept; ?>
									</td>
									<td class="text-center">
										<b><?php echo utf8_encode($gasto->type); ?></b>
									</td>
									<td class="text-center">
										<?php echo $gasto->typePayment; ?>
										
									</td>
									<td class="text-center">
										<?php echo $gasto->PayFor; ?>
									</td>
									<td class="text-center">
										<b><?php echo number_format(abs($gasto->import),2,',','.') ; ?>€</b>
									</td>
									<td class="text-center">
										<?php echo $gasto->comment; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<td class="text-center" colspan="5" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
									TOTAL GASTOS ANUALES
								</td>
								<td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
									<b><?php echo number_format(abs($total),2,',','.') ; ?>€</b>
								</td>
								<td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
									&nbsp;
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-xs-12 col-md-4">
					<h2 class="text-center font-w300 push-20">
						RESUMEN DE GASTOS
						<?php if ($firstLoad): ?>
							<span class="font-w600"><?php echo $date->copy()->format('Y') ?></span>
						<?php else: ?>
							<span class="font-w600"><?php echo strtoupper($date->copy()->formatLocalized('%B %Y')) ?></span>
						<?php endif ?>
					</h2>
					<div class="col-xs-12 not-padding push-20 table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
							    <tr>							       
							        <th>Tipo</th>
							        <th>Importe</th>
							        <th>% PORCENTAJE</th>
							    </tr>
							</thead>
							<tbody>
								<?php foreach ($gastosMes as $key => $gastoMes): ?>
								   	<tr>
									    <td><?php echo utf8_encode($gastoMes->type)?></td>
									        <?php $gastototal = 0; ?>
									        <?php foreach ($gastos as $gasto): ?>
									        	<?php if ($gastoMes->type == $gasto->type): ?>
									        		<?php $gastototal +=  $gasto->import?>
									        	<?php endif ?>
									        <?php endforeach ?>
								        <td class="text-center"><?php echo number_format(abs($gastototal),2,',','.')?>€</td>							        
								        <td class="text-center">
								        	<?php $percent = (abs($gastototal) * 100) / abs($total) ?>
								        	<?php echo number_format($percent,2,',','.')?>%
								        </td>								        
								    </tr>
								<?php endforeach; ?>
								<tr>
									<td style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
										<b>TOTAL</b>
									</td>
									<td style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;" class="text-center" colspan="2" ><b><?php echo number_format(abs($total),2,',','.') ; ?>€</b></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			<?php else: ?>
				<div class="col-sm-4 col-sm-offset-4">
					<h1 class="font-s128 font-w300 text-primary animated bounceInDown">Lo siento</h1>
					<h2 class="h2 font-w300 push-50 animated fadeInUp">No hay gastos disponibles para este mes</h2>
				</div>
			<?php endif ?>
		</div>
	</section>
</div>

<div class="modal fade" id="modal-ingreso" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="block block-themed block-transparent remove-margin-b">
				<div class="block-header bg-primary-dark">
					<ul class="block-options">
						<li>
							<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
						</li>
					</ul>
				</div>
				<div class="row block-content" id="contentListGasto">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal in" id="modal-csv" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
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
				<div class="row block-content">
					<section class="content content-boxed">
						<form action="{{url('/admin/gastos/import/csv/')}}" method="post" enctype="multipart/form-data">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
							<div class="col-xs-12 push-20">
								Selecciona fichero para subir:
							    <input type="file" name="csv" id="csv" class="form-control">
							</div>
							<div class="col-xs-12 push-20">
								<button class="btn btn-success" type="submit">
									Subir
								</button>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')

	<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>

	<script type="text/javascript">
		$(document).ready(function() {


			$('#addIngreso').click(function(){
				$.get('/admin/nuevo/gasto', function(data) {
					$('#contentListGasto').empty().append(data);
				});
			});

			$('#dateSelector').change(function() {
				var date = $(this).val();		

				window.location.replace("/admin/gastos/"+date);
			});
		});
	</script>
	
	<script type="text/javascript">
		/* Grafica Anual*/
			var data = {
			    labels: [
			    			<?php $year = $date->copy()->startOfYear(); ?>
							<?php for($i = 1 ; $i <= 12; $i++): ?>
								<?php if ($i == 12): ?>
									"<?php echo ucfirst(substr($year->formatlocalized('%B'), 0, 3)); ?>"
								<?php else: ?>
									"<?php echo ucfirst(substr($year->formatlocalized('%B'), 0, 3)); ?>",
								<?php endif; ?>
								<?php $year->addMonths(1); ?>
							<?php endfor; ?>
			    			],
			    datasets: [
			        {
			            label: "Gastos por meses",
			            backgroundColor: [
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			            ],
			            borderColor: [
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(54, 162, 235, 1)',
			            ],
			            borderWidth: 1,
			            data: [
			            		<?php for($i = 1 ; $i <= 12; $i++): ?>
			            			<?php 
			            				$totalMonth = 
			            								$summaryYear['MOBILIARIO'][$i]+
														$summaryYear['SERVICIOS PROFESIONALES INDEPENDIENTES'][$i]+
														$summaryYear['VARIOS'][$i]+
														$summaryYear['EQUIPAMIENTO DEPORTIVO'][$i]+
														$summaryYear['IMPUESTOS'][$i]+
														$summaryYear['SUMINISTROS'][$i]+
														$summaryYear['GASTOS BANCARIOS'][$i]+
														$summaryYear['PUBLICIDAD'][$i]+
														$summaryYear['REPARACION Y CONSERVACION'][$i]+
														$summaryYear['ALQUILER NAVE'][$i]+
														$summaryYear['SEGUROS SOCIALES'][$i]+
														$summaryYear['NOMINAS'][$i]+
														$summaryYear['TARJETA VISA'][$i]+
														$summaryYear['MATERIAL OFICINA'][$i]+
														$summaryYear['MENSAJERIA'][$i]+
														$summaryYear['PRODUCTOS VENDING'][$i]+
														$summaryYear['LIMPIEZA'][$i]+
														$summaryYear['INTERNET'][$i]+
														$summaryYear['RENTING EQUIPAMIENTO DEPORTIVO'][$i]+
														$summaryYear['COMISONES COMERCIALES'][$i];
			            			?>
		            				<?php if ($i == 12): ?>
		            					<?php echo abs($totalMonth); ?>
		            				<?php else: ?>
		            					<?php echo abs($totalMonth); ?>,
		            				<?php endif ?>
			            		<?php endfor; ?>
			            		],
			        }
			    ]
			};

			var myBarChart = new Chart('barChart', {
			    type: 'line',
			    data: data,
			    option: {}
			});


		/*Grafica de queso*/
			/* La configuracion de posicion funciona con la siguiente que se escriba*/
			Chart.defaults.global.legend.position = 'bottom';
			Chart.defaults.global.legend.labels.usePointStyle = true;
			/*Fin de configuracion de posicion*/

			var dataPie = {
			    labels: [
						"ALQUILER",
						"S. SOCIALES",
						"NOMINAS",
						"PUBLICIDAD",
						"SUMINISTROS",
						" VISA",
						"OTROS",


			    ],
			        datasets: [
			            {
				            data: 	[

					    	       		<?php echo abs( array_sum($summaryYear["ALQUILER NAVE"]));?> ,
					    	       		<?php echo abs( array_sum($summaryYear["SEGUROS SOCIALES"]));?> ,
					    	       		<?php echo abs( array_sum($summaryYear["NOMINAS"]));?> ,
					    	       		<?php echo abs( array_sum($summaryYear["PUBLICIDAD"]));?> ,
					    	       		<?php echo abs( array_sum($summaryYear["SUMINISTROS"]));?> ,
					    	       		<?php echo abs( array_sum($summaryYear["TARJETA VISA"]));?> ,
					    	       		<?php echo abs( 
					    	       					array_sum($summaryYear["MOBILIARIO"]) +
					    	       					array_sum($summaryYear["SERVICIOS PROFESIONALES INDEPENDIENTES"]) +
					    	       					array_sum($summaryYear['VARIOS']) +
					    	       					array_sum($summaryYear['EQUIPAMIENTO DEPORTIVO']) +
					    	       					array_sum($summaryYear['IMPUESTOS']) +
					    	       					array_sum($summaryYear['GASTOS BANCARIOS']) +
					    	       					array_sum($summaryYear['REPARACION Y CONSERVACION']) +
					    	       					array_sum($summaryYear['MATERIAL OFICINA']) +
					    	       					array_sum($summaryYear['MENSAJERIA']) +
					    	       					array_sum($summaryYear['PRODUCTOS VENDING']) +
					    	       					array_sum($summaryYear['LIMPIEZA']) +
					    	       					array_sum($summaryYear['INTERNET']) +
					    	       					array_sum($summaryYear['RENTING EQUIPAMIENTO DEPORTIVO']) +
					    	       					array_sum($summaryYear['COMISONES COMERCIALES']) 
					    	       			)?>
			                		],
			            
			            backgroundColor: 	[
								                "#388E3C",
								                "#EF6C00",
								                "#01579B",
								                "#00BFA5",
								                "#FF5252",
								                "#4A148C",
								                "#00E5FF"
			            					],
			            
			            hoverBackgroundColor: 	[
									               	"#388E3C",
									                "#EF6C00",
									                "#01579B",
									                "#00BFA5",
									                "#FF5252",
									                "#4A148C",
									                "#00E5FF"
			            						],			        
			        }
			    ]		    
			};


			var myPieChart = new Chart('pieChart',{
			    type: 'pie',
			    data: dataPie,
			    options: {
			         // legend: {
			         //    display: false
			         // }
			    }
			});

	</script>
@endsection