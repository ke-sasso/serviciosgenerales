<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<?php 
	
	$fechaIngreso = date('d-m-Y');
?>
<body>
	<h4>Se le notifica que:</h4>
	<p>El empleado: <b>{{ $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado }}</b>
	ha ingresado al sistema de servicios generales una solicitud de: <b>{{$tipo}}.</b>
	</p>
	@if($idTipo==1)
	<p><b>FECHA DE INGRESO:</b> {{ date('d-m-Y') }}</p>
	<p><b>UNIDAD:</b> {{$unidad->nombreUnidad}}</p>
	<p><b>MOTIVO:</b> {!!$motivo->nombre!!}</p>
	<p><b>FECHA DEL PERMISO:</b> {{$solicitud->fechaPermiso}}</p>
	<p><b>OBSERVACIONES:</b>{{$solicitud->observaciones}}</p>
	
	<!--
	<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolNoMarca,'idEstado' => 3])}}">Autorizar</a>
		
	<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolNoMarca,'idEstado' => 2])}}">Denegar</a>
	-->
	<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/3">Autorizar</a>
		
	<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/2">Denegar</a>
	@endif
	
	@if($idTipo==2)
		<p><b>FECHA DE INGRESO:</b>{{ date('d-m-Y') }}</p>
		<p><b>UNIDAD:</b> {{$unidad->nombreUnidad}}</p>
		<p><b>MOTIVO:</b> {!!$motivo->nombre!!}</p>
		<p><b>FECHA DEL INICIO:</b> {{$solicitud->fechaInicio}}</p>
		<p><b>FECHA DEL FIN:</b> {{$solicitud->fechaFin}}</p>
		<!-- <p><b>NUMERO DE DÍAS:</b> {{$solicitud->dias}}</p>-->
		<p><b>OBSERVACIONES:</b>{{$solicitud->observaciones}}</p>
	
	<!--
	<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolLicencia,'idEstado' => 3])}}">Autorizar</a>
		
	<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolLicencia,'idEstado' => 2])}}">Denegar</a>
	-->

	<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolLicencia }}/3">Autorizar</a>
		
	<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolLicencia }}/2">Denegar</a>

	@endif
</body>
</html>