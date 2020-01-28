<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="ADE">
		<meta name="keywords" content="ADE,ade">
		<meta name="author" content="Unidad de Informática">
		<link href="{{{ asset('img/favicon.ico') }}}" rel="shortcut icon">
		<title>~ Solicitudes Administrativas ~</title>
 
		<!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
		{!! Html::style('css/bootstrap.min.css') !!} 
		
		
		{!! Html::style('plugins/font-awesome/css/font-awesome.min.css') !!} 
		{!! Html::style('css/style.css') !!} 
		{!! Html::style('css/style-responsive.css') !!} 
 		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
 
	<body class="login tooltips">
		<!--
		===========================================================
		BEGIN PAGE
		===========================================================
		-->
		<div class="login-header text-center">
			<img src="{{ asset('img/logo-login.png') }}" class="logo" alt="Logo">
		</div>
		<div class="login-wrapper">
			@if($errors->any())
			<div class="alert alert-warning alert-bold-border fade in alert-dismissable">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			  <strong>Oops!</strong> Debes corregir los siguientes errores para poder continuar		
					<ul class="inline-popups">
						@foreach ($errors->all() as $error)
							<li  class="alert-link">{{ $error }}</li>
						@endforeach
					</ul>
			</div>
			@endif
			<form role="form" action="{{ url('/login') }}" method="post">
				<div class="form-group has-feedback lg left-feedback no-label">
				  {!! Form::text('txtUsuario',null,['class'=>'form-control no-border input-lg rounded','placeholder'=>'Usuario','autofocus'=>'true']) !!}
				  <span class="fa fa-user form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback lg left-feedback no-label">
				  {!! Form::password('txtContrasenia',['class'=>'form-control no-border input-lg rounded','placeholder'=>'Contraseña']) !!}
				  <span class="fa fa-unlock-alt form-control-feedback"></span>
				</div>

				<div class="form-group">
					<input type="hidden" name="_token" value="{{ csrf_token() }}"/>
					<button type="submit" class="btn btn-info btn-lg btn-perspective btn-block">Iniciar Sesión</button>
				</div>
			</form>
	
		<!-- END PAGE CONTENT -->
			
		<!--
		===========================================================
		END PAGE
		===========================================================
		-->
		
		<!--
		===========================================================
		Placed at the end of the document so the pages load faster
		===========================================================
		-->
		{!! Html::script('js/jquery.min.js') !!}
		{!! Html::script('js/bootstrap.min.js') !!}
	</body>
</html>