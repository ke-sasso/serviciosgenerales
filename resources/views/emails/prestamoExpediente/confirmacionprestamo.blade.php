<!DOCTYPE html>
<html lang="es">
<head>
	<title>CONFIRMACION</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
 <style>
 	body{
 		background: #EFF8FB;
 		width: 60%;
 		margin:0 auto;
 	}
 </style>
</head>
<body>

	@if($estado==4)	
		<div class="panel panel-primary" style="margin-top: 10%;">
			<div class="panel-heading">CONFIRMACIÓN DE SOLICITUD DE PRÉSTAMO DE EXPEDIENTE</div>
			<div class="panel-body">								
			<p>La solicitud de préstamo fue <b>APROBADA</b> exitosamente.</p>			
			</div>
		</div>
	@endif
	@if($estado==6)
		<div class="panel panel-danger" style="margin-top: 10%;">
			<div class="panel-heading">CONFIRMACIÓN DE SOLICITUD DE PRÉSTAMO DE EXPEDIENTE</div>
			<div class="panel-body">								
			<p>La solicitud de préstamo fue <b>DENEGADA</b> exitosamente.</p>			
			</div>
		</div>				
	@endif
	@if($estado==10)
		<div class="panel panel-danger" style="margin-top: 10%;">
			<div class="panel-heading">CONFIRMACIÓN DE SOLICITUD DE PRÉSTAMO DE EXPEDIENTE</div>
			<div class="panel-body">								
			<p>La solicitud de préstamo fue <b>DESISTIDA POR EL USUARIO SOLICITANTE</b>.</p>			
			</div>
		</div>				
	@endif
</body>
</html>