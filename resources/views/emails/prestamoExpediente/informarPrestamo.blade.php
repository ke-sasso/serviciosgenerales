<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h4>Se le notifica que:</h4>
	<p><b>El empleado:</b> {{ $empleado->nombresUsuario.' '.$empleado->apellidosUsuario }}	
	</p>
	<p><b>EN:</b> {{$unidad->nombreUnidad}}</p>
	<br>
	<p><b>Ha ingresado una solicitud de:</b> PRÉSTAMO DE EXPEDIENTE PARA {{$tipoprestamo}}</p>
	<p><b>EXPEDIENTE :</b> {{$prestamo->noRegistroExpediente}} - {{$prestamo->nombreExpediente}}</p>
	<p><b>FECHA DE INGRESO:</b>{{ date('d-m-Y') }}</p>
	
</body>
</html>