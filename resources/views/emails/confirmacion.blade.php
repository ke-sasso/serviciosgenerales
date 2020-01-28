<!DOCTYPE html>
<html lang="es">
<head>
	<title>SOLICITUDES Y PERMISOS</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

</head>
<body>

	<div class="panel panel-default">
		<div class="panel-body">
			@if($autorizada==1)
				@if($solicitud->idEstadoSol==3 || $solicitud->idEstadoSol==4 || $solicitud->idEstadoSol==6)
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
						<div class="panel-body">
							<p> La solicitud ha sido autorizada, con la siguiente información: </p>
							<table>
								<tr>
									<td><b>Solicitante: </b></td>
									<td>{{' '.$solicitante->nombresUsuario.' '.$solicitante->apellidosUsuario}}</td>
								</tr>
								<tr>
									<td><b>Unidad: </b></td>
									<td>{{$unidad->nombreUnidad}}</td>
								</tr><tr>
									<td><b>Motivo:</b></td>
									<td>{{$motivo->nombre}}</td>
								</tr><tr>
									<td><b>Fecha: </b></td>
									<td>{{$solicitud->fechaApruebaDenegar}}</td>
								</tr><tr>
									<td><b>ESTADO:</b></td>
									<td>AUTORIZADA</td>
								</tr>
							</table>
						</div>
					</div>
				@elseif($solicitud->idEstadoSol==2)
					<div class="panel panel-danger">
						<!-- Default panel contents -->
						<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
						<div class="panel-body">
							<p> La solicitud ha sido denegada, con la siguiente información: </p>
							<table>
								<tr>
									<td><b>Solicitante: </b></td>
									<td>{{' '.$solicitante->nombresUsuario.' '.$solicitante->apellidosUsuario}}</td>
								</tr>
								<tr>
									<td><b>Unidad: </b></td>
									<td>{{$unidad->nombreUnidad}}</td>
								</tr><tr>
									<td><b>Motivo:</b></td>
									<td>{{$motivo->nombre}}</td>
								</tr><tr>
									<td><b>Fecha: </b></td>
									<td>{{$solicitud->fechaApruebaDenegar}}</td>
								</tr><tr>
									<td><b>ESTADO:</b></td>
									<td>{{$solicitud->nombre}}</td>
								</tr>
							</table>
						</div>
					</div>
				@endif
			@endif
			
			@if($autorizada==0)
				@if($solicitud->idEstadoSol==2)
				<div class="panel panel-danger">
				<!-- Default panel contents -->
				<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
				<div class="panel-body">
				  
					<p> La solicitud ha sido denegada.</p>
				  
				</div>
				</div>
				@endif
				
				@if($solicitud->idEstadoSol==3)
				<div class="panel panel-success">
				<!-- Default panel contents -->
				<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
				<div class="panel-body">
				  
					<p> La solicitud ya ha sido autorizada por el jefe inmediato.</p>
				  
				</div>
				</div>
				@endif
				
				@if($solicitud->idEstadoSol==4)
				<div class="panel panel-success">
				<!-- Default panel contents -->
				<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
				<div class="panel-body">
				  
					<p> La solicitud ya ha sido autorizada por el jefe superior.</p>
				  
				</div>
				</div>
				@endif
				
				@if($solicitud->idEstadoSol==5)
				<div class="panel panel-warning">
				<!-- Default panel contents -->
				<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
				<div class="panel-body">
				  
					<p> La solicitud ha sido sido cancelada por el empleado que la solicito..</p>
				  
				</div>
				</div>
				@endif
				
				@if($solicitud->idEstadoSol==7)
				<div class="panel panel-warning">
				<!-- Default panel contents -->
				<div class="panel-heading">SOLICITUDES Y PERMISOS - DNM</div>
				<div class="panel-body">
				  
					<p> La solicitud ha sido desistida por el usuario.</p>
				  
				</div>
				</div>
				@endif

			@endif
		</div>
	</div>
</body>
</html>