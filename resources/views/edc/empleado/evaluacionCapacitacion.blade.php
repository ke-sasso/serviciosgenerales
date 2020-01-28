@extends('master')
@section('css')
{!! Html::style('plugins/bootstrap-modal/css/bootstrap-modal.css') !!}
{!! Html::style('plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css') !!}
<style type="text/css">
.entry:not(:first-of-type)
{
    margin-top: 10px;
}

.glyphicon
{
    font-size: 12px;
}
.text-uppercase
{ text-transform: uppercase; }
</style>		
@endsection
@section('contenido')
{{-- MENSAJE DE EXITO --}}
@if(Session::has('msnExito'))
	<div class="alert alert-success square fade in alert-dismissable">
		<button class="close" aria-hidden="true" data-dismiss="alert" type="button">x</button>
		<strong>Enhorabuena!</strong>
		{{ Session::get('msnExito') }}
	</div>
@endif
{{-- MENSAJE DE ERROR --}}
@if(Session::has('msnError'))
	<div class="alert alert-danger square fade in alert-dismissable">
		<button class="close" aria-hidden="true" data-dismiss="alert" type="button">x</button>
		<strong>Atención: </strong>{{ Session::get('msnError') }}
	</div>
@endif

<div class="the-box">
	<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
		<div class="input-group form-group">
			<div class="input-group-addon">Tema de la capacitaci&oacute;n</div>
			<label class="form-control">{{$capacitacion->nombreCapacitacion}}</label>
		</div>
		<div class="input-group form-group">
			<div class="input-group-addon">Entidad que la Imparte</div>
			<label class="form-control">{{$instituto[0]->nombreInstitucion}}</label>
		</div>
		<div class="input-group form-group">
			<div class="input-group-addon">Lugar donde se Desarroll&oacute; el Evento</div>
			<label class="form-control">{{$capacitacion->lugar}}</label>
		</div>
		<div class="input-group form-group">
			<div class="input-group-addon">Nombre del Instructor</div>
			<label class="form-control">{{$capacitacion->instructor}}</label>
		</div>
		<div class="input-group form-group">
			<div class="input-group-addon">Fecha de la Capacitaci&oacute;n</div>
			<label class="form-control">{{date('d-m-Y',strtotime($capacitacion->fechaDesde))}} al {{date('d-m-Y',strtotime($capacitacion->fechaHasta))}}</label>
		</div>
		
	</div>	
	<form id="formEvaluacion" method="post" action="{{ route('store.evaluacion.capacitaciones') }}"  autocomplete="off">
		<div class="table-responsive">
			<table class="table table-th-block table-success table-striped table-hover table-bordered" width="100%">
				<thead>
					<tr>
						<th>No</th>
						<th>Clasificación</th>
						<th>Ítem</th>
						<th>Opciones</th>
					</tr>
				</thead>
				<tbody>
					@foreach($items as $item)
					<tr>
						<td>{{$item->idItem}}</td>
						<td>{{$item->grupoItem}}</td>
						<td>{{$item->nombreItem}}</td>
						<td>
						@if($item->idItem >= 1 && $item->idItem <= 4)
							<select name="opcion[{{$item->idItem}}]" class="form-control" placeholder="Elija una opción">
								<option value="">Seleccione...</option>
								<option value="0">
									SI
								</option>
								<option value="1">
									NO
								</option>
							</select>
						@else
							<select name="opcion[{{$item->idItem}}]" class="form-control">
								<option value="">Seleccione...</option>
								
								<option value="2">
									TOTAL
								</option>
								<option value="3">
									PARCIAL
								</option>
								<option value="4">
									MÍNIMA/O
								</option>
							</select>
						@endif
						</td>
					</tr>
					@endforeach
					
				</tbody>
			</table>
		</div>	
		
		<div align="center">
		   	<input type="hidden" name="idCapacitacion" value="{{$capacitacion->idCapacitacion}}">
		   	<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}" />
			
			<button type="button" id="btnEnviar" class="btn btn-primary btn-perspective">Enviar Calificación</button>
		 </div>
		
	</form>				
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script>
	$(document).ready(function () {
	 	$('#btnEnviar').on('click', function(event) {
	 		$('#formEvaluacion').submit();
	 	});		
	});

</script>
@endsection