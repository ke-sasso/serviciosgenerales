<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="Unidad de Informática">
		<link href="{{{ asset('img/faviconADE.ico') }}}" rel="shortcut icon">
		<title>DNM | 503</title>
 
		<!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
		{!! Html::style('css/bootstrap.min.css') !!} 
		
		<!-- MAIN CSS (REQUIRED ALL PAGE)-->
		{!! Html::style('plugins/font-awesome/css/font-awesome.min.css') !!} 
		{!! Html::style('css/style.css') !!} 
		{!! Html::style('css/style-responsive.css') !!} 
 
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
 
	<body class="tooltips"  style="background: #B25D6B;">
		
		<!--
		===========================================================
		BEGIN PAGE
		===========================================================
		-->
		<div class="login-header text-center">
			{!! Html::image('img/logo-login.png','',['class'=>'logo','alt'=>'Logo']) !!}

		</div>
		<div class="login-wrapper">
			<p class="text-center text-warning"><h3><strong>Oops!</strong></h3> Error en el servidor.</p>
			<h1 class="error-number">5<i class="fa fa-meh-o icon-xl icon-square icon-primary" style="background: #B25D6B;"></i>3</h1>
			
			<p class="text-center"><strong><a href="{{ url('/') }}">Regresar al Inicio</a></strong></p>
		</div><!-- /.login-wrapper -->
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
		<!-- MAIN JAVASRCIPT (REQUIRED ALL PAGE)-->
		{!! Html::script('js/jquery.min.js') !!}
		{!! Html::script('js/bootstrap.min.js') !!}
		
	</body>
</html>