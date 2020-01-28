<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="description" content="ECM">
		<meta name="keywords" content="Transporte,TRANSPORTE">
		<meta name="author" content="Unidad de Informática">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link href="{{{ asset('img/favicon.ico') }}}" rel="shortcut icon">
		<title>~Solicitudes Administrativas~</title>
 
		<!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
		{!! Html::style('css/bootstrap.min.css') !!} 
		<!--<link href="" rel="stylesheet">-->
		
		<!-- PLUGINS CSS -->
		{!! Html::style('plugins/weather-icon/css/weather-icons.min.css') !!} 
		{!! Html::style('plugins/prettify/prettify.min.css') !!} 
		{!! Html::style('plugins/magnific-popup/magnific-popup.min.css') !!} 
		{!! Html::style('plugins/owl-carousel/owl.carousel.min.css') !!} 
		{!! Html::style('plugins/owl-carousel/owl.theme.min.css') !!} 
		{!! Html::style('plugins/owl-carousel/owl.transitions.min.css') !!} 
		{!! Html::style('plugins/chosen/chosen.min.css') !!} 
		{!! Html::style('plugins/icheck/skins/all.css') !!} 
		{!! Html::style('plugins/datepicker/bootstrap-datepicker.css') !!} 
		{!! Html::style('plugins/timepicker/bootstrap-timepicker.min.css') !!} 
		{!! Html::style('plugins/validator/bootstrapValidator.min.css') !!} 
		{!! Html::style('plugins/summernote/summernote.min.css') !!} 
		{!! Html::style('plugins/summernote/summernote.min.css') !!} 
		{!! Html::style('plugins/markdown/bootstrap-markdown.min.css') !!} 
		{!! Html::style('plugins/datatable/css/bootstrap.datatable.min.css') !!} 
		{!! Html::style('plugins/morris-chart/morris.min.css') !!} 
		{!! Html::style('plugins/c3-chart/c3.min.css') !!} 
		{!! Html::style('plugins/slider/slider.min.css') !!}
		{!! Html::style('plugins/alertifyjs/css/alertify.min.css') !!} 
		{!! Html::style('plugins/alertifyjs/css/themes/default.min.css') !!} 

		<!-- MAIN CSS (REQUIRED ALL PAGE)-->
		{!! Html::style('plugins/font-awesome/css/font-awesome.min.css') !!} 
		{!! Html::style('css/style.css') !!} 
		{!! Html::style('css/style-responsive.css') !!} 
 		
		@yield('css')

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
 
	<body class="tooltips">
		<!--
		===========================================================
		BEGIN PAGE
		===========================================================
		-->
		<div class="wrapper">
			@include('layouts.top-nav')
			
			@include('layouts.sidebar-left-menu')
			
			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content {{ (Session::get('cfgHideMenu',false))?'toggle':'' }}">
				<div class="container-fluid">
				
				<!-- Begin page heading -->
				<h1 class="page-heading">{{ $title }} <small>{{ $subtitle }}</small></h1>
				<!-- End page heading -->
				@if (!empty($breadcrumb))
					<!-- Begin breadcrumb -->
					<ol class="breadcrumb default square rsaquo sm">
						<li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
						@if(count($breadcrumb)>1)
							@for ($i = 0; $i < count($breadcrumb) ; $i++)
								@if(($i+1) == count($breadcrumb))
									<li class="active">{{ $breadcrumb[$i]['nom'] }}</li>
								@else
									<li><a href="{{ $breadcrumb[$i]['url'] }}">{{ $breadcrumb[$i]['nom'] }}</a></li>
								@endif
							@endfor
						@else
							<li class="active">{{ $breadcrumb[0]['nom']}}</li>					
						@endif
					</ol>
					<!-- End breadcrumb -->
				@endif					
				@include('errors.errors')	
					
				@yield('contenido')
								
				</div><!-- /.container-fluid -->
				
				<!-- BEGIN FOOTER -->
				<footer>
					&copy; 2015 <a href="#">Dirección Nacional de Medicamentos</a><br />
					Diseñado por <a href="#">IT DNM</a>
				</footer>
				<!-- END FOOTER -->
				
				
			</div><!-- /.page-content -->
		</div><!-- /.wrapper -->
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
		<!-- MAIN JAVASRCIPT (REQUIRED ALL PAGE)-->
		{!! Html::script('js/jquery.min.js') !!}
		{!! Html::script('js/bootstrap.min.js') !!}
		{!! Html::script('plugins/retina/retina.min.js') !!}
		{!! Html::script('plugins/nicescroll/jquery.nicescroll.js') !!}
		{!! Html::script('plugins/slimscroll/jquery.slimscroll.min.js') !!}
		{!! Html::script('plugins/backstretch/jquery.backstretch.min.js') !!}
 		<!-- MAIN APPS JS -->
		{!! Html::script('js/apps.js') !!}
		<!-- PLUGINS -->
		{!! Html::script('plugins/skycons/skycons.js') !!}
		{!! Html::script('plugins/prettify/prettify.js') !!}
		{!! Html::script('plugins/magnific-popup/jquery.magnific-popup.min.js') !!}
		{!! Html::script('plugins/owl-carousel/owl.carousel.min.js') !!}
		{!! Html::script('plugins/chosen/chosen.jquery.min.js') !!}
		{!! Html::script('plugins/icheck/icheck.min.js') !!}
		//{!! Html::script('plugins/datepicker/bootstrap-datepicker.js') !!}
		{!! Html::script('plugins/timepicker/bootstrap-timepicker.js') !!}
		{!! Html::script('plugins/mask/jquery.mask.min.js') !!}
		{!! Html::script('plugins/validator/bootstrapValidator.min.js') !!}
		{!! Html::script('plugins/datatable/js/jquery.dataTables.min.js') !!}
		{!! Html::script('plugins/datatable/js/bootstrap.datatable.js') !!}
		{!! Html::script('plugins/summernote/summernote.min.js') !!}
		{!! Html::script('plugins/markdown/markdown.js') !!}
		{!! Html::script('plugins/markdown/to-markdown.js') !!}
		{!! Html::script('plugins/markdown/bootstrap-markdown.js') !!}
		{!! Html::script('plugins/slider/bootstrap-slider.js') !!}
		{!! Html::script('plugins/alertifyjs/alertify.min.js') !!}
		{!! Html::script('plugins/morris-chart/morris.min.js') !!}
		{!! Html::script('plugins/morris-chart/raphael.min.js') !!}

		@yield('js')

		

		<script type="text/javascript">
			function changeConfigMenu()
			{
				$.ajax({
					url:   "{{url('cfg/menu')}}",
            		type:  'get'
				});
			}
		</script>

	</body>
</html>
