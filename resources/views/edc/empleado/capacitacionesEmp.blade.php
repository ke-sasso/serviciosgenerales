@extends('master')
@section('css')
<style type="text/css">
	.text-uppercase
	{
		text-transform: uppercase;
	}
</style>
@endsection
@section('contenido')
	
	<div class="modal fade" id="modal-id">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form id="frmCapacitacion">
				<input type="hidden" name="_token" value="{{ csrf_token() }}" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Crear Plan de Capacitaciones</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="input-group">
									<div class="input-group-addon">Nombre Capacitación</div>
									<input type="text" class="form-control text-uppercase" name="nombreCapacitacion" id="nombreCapacitacion" >
								</div>
							</div>
						</div>
						<br>
						<br>
						<div class="form-group">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="input-group">
									<div class="input-group-addon">Desde</div>
									<input type="text" class="form-control datepicker" id="fechaDesde" name="fechaDesde">
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="input-group">
									<div class="input-group-addon">Fecha Hasta</div>
									<input type="text" class="form-control datepicker" id="fechaHasta" name="fechaHasta" >
								</div>
							</div>
						</div>
						<br>
						<br>
						<div class="form-group">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="input-group">
									<div class="input-group-addon">Evaluación de Desempeño</div>
									<select name="idEvaluacion" id="idEvaluacion" class="form-control">
										
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" id="btnSend" class="btn btn-primary">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">
			
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover" id="dt-capacitaciones">
					<thead>
						<tr>
							<th></th>
							<th>Nombre Capacitación</th>
							<th>Fecha Desde</th>
							<th>Fecha Hasta</th>
							<th>Evaluación</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
@section('js')
<script type="text/javascript">
	$(document).ready(function() {
		
	});
	var dtcapacitaciones =	$('#dt-capacitaciones').DataTable(
	{
			processing: true,
			serverSide: true,
			filter: false,
			ajax: {
				'url': "{{route('rh.list.capacitaciones.emp')}}"
			},
			columns: [				
				{data: 'idCapacitacion', name: 'row'},
				{data: 'nombreCapacitacion', name: 'nombreCapacitacion'},
				{data: 'fechaDesde', name:'fechaDesde'},
				{data: 'fechaHasta', name:'fechaHasta'},
				{data: 'nombre', name:'nombre'},
				{data: 'editar', name: 'editar',searchable:false,orderable:false},
			],
		    language: {
		        "sProcessing": '<div class=\"dlgwait\"></div>',
		        "url": "{{ asset('plugins/datatable/lang/es.json') }}"
		        
		        
		    },
			"order": [[ 4, 'asc' ]]
			
	});	

</script>
@endsection
