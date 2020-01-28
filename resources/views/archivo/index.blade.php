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

<div class="the-box">

	<div class="row">
		@if(!empty($exp))
			@foreach($exp as $e)
				<a href="{{route('arc.prestamo.exp.prod',[Crypt::encrypt($e->idRegistroExpediente)])}}">
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-book fa-4x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div>{{$e->nombreRegistroExpediente}}<br>{{$e->unidad->nombreUnidad}}</div>
									</div>
								</div>
							</div>
								<div class="panel-footer">
									<span class="pull-left"></span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
						</div>
					</div>
				</a>
			@endforeach
		@endif
	</div>         	

</div>

	
@endsection

@section('js')
<script type="text/javascript">
	
</script>
@endsection
