@extends('master')
@section('css')
{!! Html::style('plugins/bootstrap-modal/css/bootstrap-modal.css') !!}
{!! Html::style('plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css') !!}
<style type="text/css">
.entry:not(:first-of-type)
{
    margin-top: 10px;
}

.glyphicon
{
    font-size: 12px;
}
.text-uppercase
{ text-transform: uppercase; }
</style>		
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
	<h4 class="small-title">Nueva Solicitud de NO Marcaci&oacute;n: </h4>
							
		<form id="formNoMarcacion" method="post" action="{{ route('guardar.no.marcacion') }}" autocomplete="off">
					<div class="row">
						<div class="col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
							<label>Fecha de Solicitud:</label>
							<input type="text" name="fechaSol" class="form-control" value="{{date_format(date_create(date('Y/m/d')),'d/m/Y')}}" disabled>
							</div>	
						</div>	
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
							<label>Nombre del Empleado (a):</label>
							<input type="text" name="nomEmpleado" class="form-control" value="{!! $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado !!}" readonly>
							</div>	
						</div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<div class="form-group">
							<label>Unidad/Departamento:</label>
							<input type="text" name="unidad" class="form-control" value="{!! $empleado->nombreUnidad !!}" readonly>
							</div>	
						</div>
					</div>
					<label><b>MOTIVOS: </b></label>
					<br>
					@foreach($motivos as $motivo)
						@if($motivo->idMotivo!=25)
							<div class="row">
								<div class="col-sm-12 col-md-12 col-lg-12">
									<div class="form-group">
										<div class="col-sm-12 col-md-4 col-lg-4">
										{!!$motivo->nombre!!} 
										</div>
										<div class="col-sm-12 col-md-4 col-lg-4">
										<input type="checkbox" class="motivo"  name="motivo" value="{{$motivo->idSolMot}}">
										</div>
									</div>
								</div>
							</div>
						@endif
					@endforeach

					<br>
					<div class="row">
					<!--FECHA DE SOLICITUD DESDE-->
					<div class="col-sm-3">
						<div class="hero-unit">
						<div class="form-group">
						{!! Form::label('fechaSolicitud', 'Fecha del permiso:') !!}
						{!! Form::text('fechaSolicitud',null,['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'yyyy-mm-dd','placeholder'=>'dd-mm-yyyy', 'required'])!!}
						
						</div><!-- /.form-group -->
						</div>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-3">
						<div class="form-group">
						{!! Form::label('horaEntrada', 'Hora Entrada:') !!}
							<div class="input-group input-append bootstrap-timepicker">
								{!! Form::text('horaEntrada',null,['id'=>'timepicker1','class' => 'form-control timepicker ', 'required'])!!}
								<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
							</div>
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-6 -->
							
							
					<div class="col-sm-3">
						<div class="form-group">
						{!! Form::label('horaSalida', 'Hora Salida:') !!}
							<div class="input-group input-append bootstrap-timepicker">
								{!! Form::text('horaSalida',null,['id'=>'timepicker2','class' => 'form-control timepicker', 'required'])!!}
								<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
							</div>
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-6 -->
							
							<!--/FECHA DE SOLICITUD DESDE-->

								
				</div>
				<div class="row">		  
	                <div class="col-md-10 col-lg-10">
						<div class="form-group" >
								{!! Form::label('Observaciones', 'Observaciones:') !!}
								<textarea name="observaciones" rows="2" class="form-control"></textarea>
						</div>
					</div>
				</div>												

			  <div class="from-group">
			 	<div align="center">
			   <input type="hidden" name="_token" value="{{ csrf_token() }}" />
					 <button type="submit" class="btn btn-primary btn-perspective">Guardar</button>
			  </div>
			  </div>
								  		
										
		</form>
						
	
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script>
	$(document).ready(function () {
	 	
			$('#fechaSolicitud').datepicker({ 
                    autoclose:true
			    });
				
	    $('#timepicker1').timepicker({
   			minuteStep:5
		  });
	   

       	$('#timepicker2').timepicker({
		   		minuteStep:5
		   	});

     $('input[class="motivo"]').click(function(){
	    var $inputs=$('input[class="motivo"]');
	    if ($(this).is(':checked')) {
	      if($(this).val()==23){
	    		$('.derma').prop('disabled', false);
	    	}
	    	else{
	    		$('.derma').attr('checked', false); // Unchecks it
	    		$('.derma').prop('disabled', true);
	    	}
	      $inputs.not(this).prop('disabled',true);
	    }
	    else{
	      $('.derma').attr('checked', false); // Unchecks it
	      $('.derma').prop('disabled', true);
	      $inputs.prop('disabled',false); 
	    }
  	});

});

</script>
@endsection