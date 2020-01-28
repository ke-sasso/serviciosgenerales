<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h4>Se le notifica que:</h4>
	<p>El empleado: <b>{{ $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado }}</b>
	ha ingresado una solicitud de: <b>{{$tipo}}.</b>
	</p>
	@if($idTipo==1)
	<p><b>FECHA DE INGRESO:</b>{{ date('d-m-Y') }}</p>
	<p><b>UNIDAD:</b> {{$unidad->nombreUnidad}}</p>
	<p><b>MOTIVO:</b> {!!$motivo->nombre!!}</p>
	<p><b>FECHA DEL PERMISO:</b> {{$solicitud->fechaPermiso}}</p>
	<p><b>OBSERVACIONES:</b>{{$solicitud->observaciones}}</p>
	
	@if($autorizada==3)
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/6">Autorizar</a>
		<!-- <a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolNoMarca,'idEstado' => 6])}}">-->Autorizar</a>
	@elseif($autorizada==2)
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/4">Autorizar</a>
		<!--<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolNoMarca,'idEstado' => 4])}}">-->Autorizar</a>
	@endif
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/2">Denegar</a>
		<!--<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolNoMarca,'idEstado' => 2])}}">Denegar</a>-->
	@endif
	
	@if($idTipo==2)
		<p><b>FECHA DE INGRESO:</b>{{ date('d-m-Y') }}</p>
		<p><b>UNIDAD:</b> {{$unidad->nombreUnidad}}</p>
		<p><b>MOTIVO:</b> {!!$motivo->nombre!!}</p>
		<p><b>FECHA DEL INICIO:</b> {{$solicitud->fechaInicio}}</p>
		<p><b>FECHA DEL FIN:</b> {{$solicitud->fechaFin}}</p>
		<p><b>OBSERVACIONES:</b>{{$solicitud->observaciones}}</p>
	
	@if($autorizada==3)
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/6">Autorizar</a>
		<!--<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolLicencia,'idEstado' => 6])}}">Autorizar</a>-->
	@elseif($autorizada==2)
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/4">Autorizar</a>
		<!--<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolLicencia,'idEstado' => 4])}}">Autorizar</a>-->
	@endif
		<a href="http://wsexterno.medicamentos.gob.sv/soladm/auth/{{ $idTipo }}/{{ $solicitud->idSolNoMarca }}/2">Denegar</a>
		<!--<a href="{{route('correo',['idTipo' => $idTipo, 'idSolicitud' =>$solicitud->idSolLicencia,'idEstado' => 2])}}">Denegar</a>-->
	@endif
</body>
</html>