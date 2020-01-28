@extends('master')

@section('css')
	
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
			<strong>Algo ha salido mal.</strong>
				{{ Session::get('msnError') }}
		</div>
	@endif


@if(Auth::user()->idEmpleado==null)

{!!Form::open(['route' => 'actualizar.datos','method' => 'POST', 'role'=>'form'])!!}
	<div class="the-box">
	 	
	 		
		    <h3><strong>ACTUALICE SUS DATOS</strong></h3>
			<div class="row">
            	<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
					<div class="form-group">
		                <label>Nombre Completo:</label>
						<input type="text" id="nombre"name="nombre" class="form-control" disabled value="{{ Auth::user()->nombresUsuario.' '.Auth::user()->apellidosUsuario }}">           
		            </div>
            	</div>
            </div>
            <div class="row">
            	<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		            <div class="form-group">
		                <label>Código de Empleado:</label>
						<input type="text" id="codigo"name="codigo" min="1" max="4" class="form-control" required value="">           
		            </div>
	            </div>
	            <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
			 	 	<div class="form-group">
		                {!! Form::label('correo', 'Correo Electronico:') !!}
		                {!! Form::email('correo',null,['id'=>'correo','class' => 'form-control', 'required'])!!}
		            </div>
	            </div>
         	</div>
         	@if($idPlazaFuncionalPadre==null)
         		<div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
			 	 	<div class="form-group">
		                {!! Form::label('jefe', 'Seleccione quien es su jefe inmediato:') !!}
		                <select name="jefeI" id="jefeI" class="form-control" required>
		              		<option value="0">Seleccione...</option>
		              		@foreach($jefes as $jefe)
		              			<option value="{{$jefe->idPlazaFuncional}}">{{$jefe->nombrePlaza}}</option>
		              		@endforeach
		              	</select>
		            </div>
	            </div>
         		</div>
         	@endif
    
          
          	<div class="from-group">
          		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				{!!Form::submit('Guardar', ['class' => 'btn btn-primary'])!!}
			</div>

	 </div>

	{!!Form::close()!!}


@elseif(!isset($idPlazaFuncionalPadre))
{!!Form::open(['route' => 'actualizar.datos','method' => 'POST', 'role'=>'form'])!!}
	<div class="the-box">
	 	<h3><strong>ACTUALICE SUS DATOS</strong></h3>
            <div class="row">
	            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
			 	 	<div class="form-group">
		                {!! Form::label('jefe', 'Seleccione quien es su jefe inmediato:') !!}
		                <select name="jefeI" id="jefeI" class="form-control" required>
		              		<option value="0">Seleccione...</option>
		              	@if(isset($jefes))
		              		@foreach($jefes as $jefe)
		              			<option value="{{$jefe->idPlazaFuncional}}">{{$jefe->nombrePlaza}}</option>
		              		@endforeach
		              	@endif
		              	</select>
		            </div>
	            </div>
         	</div>
    
          
          	<div class="from-group">
          		<input type="hidden" name="_token" value="{{ csrf_token() }}" />
				{!!Form::submit('Guardar', ['class' => 'btn btn-primary'])!!}
			</div>

	 </div>

{!!Form::close()!!}
@else


@if(isset($unidadJefatura) && $unidadJefatura != null)

@if(!empty($dashboard))
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">SOLICITUDES DE PERMISOS</h3>
	</div>
	<div class="panel-body">
		<div class="table-responsive" id="perf_div"></div>

		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="text-center">COD. EMPLEADO</th>
						<th class="text-center">NOMBRE EMPLEADO</th>
						<th class="text-center">CANTIDAD DE PERMISOS</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($dashboard as $key => $value) {
							echo '<tr>';
							echo '<td>'.$value->idEmpleado.'</td>';
							echo '<td>'.$value->nombresEmpleado.' '.$value->apellidosEmpleado.'</td>';
							echo '<td class="text-center">'.$value->cantidad.'</td>';
							echo '</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
@endif
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">SOLICITUDES POR UNIDADES</h3>
	</div>
	<div class="panel-body">
		<div class="container-fluid dark-ful" id="unidades"></div>		
	</div>
</div>
@if(!empty($marcacionUnidad))
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">MARCACI&Oacute;N EMPLEADOS</h3>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover" id="dt-marcaje">
				<thead>
					<tr>
						<th>Cod. Empleado</th>
						<th>Nombre Empleado</th>
						<th>Fecha / Hora Marcaci&oacute;n</th>
					</tr>
				</thead>
				<tbody>
					<?php  
						foreach ($marcacionUnidad as $key => $value) {
							echo "<tr>";
							echo "<td>".$value->idEmpleado."</td>";
							echo "<td>".$value->nombresEmpleado." ".$value->apellidosEmpleado."</td>";
							echo "<td>".$value->FechaMarca."</td>";
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
@endif
<?= $lava->render('ColumnChart', 'solicitudes', 'perf_div') ?>
<?= $lava->render('ColumnChart', 'solicitudesUnidades', 'unidades') ?>
	@endif
	@endif
@endsection

@section('js')
<script type="text/javascript">
	$(document).ready(function() {
		$('#dt-marcaje').DataTable();
	});
</script>
@endsection