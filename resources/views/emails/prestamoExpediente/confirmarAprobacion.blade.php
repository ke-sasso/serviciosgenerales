<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h4>Se le notifica que:</h4>

	<p><b>La solicitud de:</b> PRÉSTAMO DE EXPEDIENTE</p>
	<p><b>EXPEDIENTE :</b> {{$prestamo->noRegistroExpediente}} - {{$prestamo->nombreExpediente}}</p>

	<p><b>Realizada por :</b> {{ $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado }}	
	</p>
	<p><b>EN:</b> {{$unidad->nombreUnidad}}</p>	
	<br>	
	<p><b>FECHA DE INGRESO:</b>{{ date('d-m-Y') }}</p>
	@if($prestamo->estadoPrestamo==4)
		<p><b>Ha sido : </b>AUTORIZADA</p>
	@endif
	@if($prestamo->estadoPrestamo==6)
		<p><b>Ha sido : </b style="color:red">DENEGADA</p>
	@endif
	
</body>
</html>