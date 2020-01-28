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
							
		<form id="formNoMarcacion" method="post" action="{{ route('autorizar.permiso') }}" autocomplete="off">
					<input type="hidden" name="idSolicitud" id="idSolicitud" value="{{$solicitud->idSolNoMarca}}">
					<input type="hidden" name="tipoPermiso" value="1">
					<div class="row">
						<div class="col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
							<label>Fecha de Solicitud:</label>
							<input type="text" name="fechaSol" class="form-control datepicker" value="{{$solicitud->fechaCreacion}}" disabled>
							</div>	
						</div>	
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
							<label>Nombre del Empleado (a):</label>
							<input type="text" name="nomEmpleado" class="form-control" value="{{$empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado }}" readonly>
							</div>	
						</div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<div class="form-group">
							<label>Unidad/Departamento:</label>
							<input type="text" name="unidad" class="form-control" value="{{$unidad->nombreUnidad}}" readonly>
							</div>	
						</div>
					</div>
					<label><b>MOTIVOS: </b></label>
					<br>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-sm-12 col-md-4 col-lg-4">
								OLVIDO DE MARCACIÓN   
								</div>
								<div class="col-sm-12 col-md-4 col-lg-4">
								@if($solicitud->motivo==21)
									<input type="checkbox" class="motivo"  checked name="motivo" value="21" disabled>
								@else
									<input type="checkbox" class="motivo"   name="motivo" value="21" disabled>
								@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-sm-12 col-md-4 col-lg-4">
								MISION OFICIAL  
								</div>
								<div class="col-sm-12 col-md-4 col-lg-4">
								@if($solicitud->motivo==22)
									<input type="checkbox" class="motivo"  checked name="motivo" value="22" disabled>
								@else
									<input type="checkbox" class="motivo"  name="motivo" value="22" disabled>
								@endif
								</div>
							</div>
						</div>
					</div>
					<!--
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-sm-12 col-md-4 col-lg-4">
								PROBLEMAS DERMATOLÓGICOS
								</div>
								<div class="col-sm-12 col-md-6 col-lg-6">
								@if($solicitud->motivo==23)
									<input type="checkbox" class="motivo" checked name="motivo" value="23" disabled>
									@if($solicitud->respaldo==1)
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Con Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox"  name="dermatologico" checked class="derma" value="1" disabled>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sin Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="dermatologico" class="derma" value="2" disabled>
									@elseif($solicitud->respaldo==2)
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Con Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox"  name="dermatologico"  class="derma" value="1" disabled>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sin Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="dermatologico" checked class="derma" value="2" disabled>
									@endif
								@else
									<input type="checkbox" class="motivo"  name="motivo" value="23" disabled>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Con Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox"  name="dermatologico" class="derma" value="1" disabled>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sin Respaldo medico &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="dermatologico" class="derma" value="2" disabled>
								@endif
								
								</div>
								
							</div>
						</div>
  								
					</div>
					-->
					<br>
					<div class="row">
					<!--FECHA DE SOLICITUD DESDE-->
					<div class="col-sm-3">
						<div class="hero-unit">
						<div class="form-group">
						{!! Form::label('fechaSolicitud', 'Fecha del permiso:') !!}
						{!! Form::text('fechaSolicitud',date_format(date_create($solicitud->fechaPermiso),'d-m-Y'),['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'yyyy-mm-dd','placeholder'=>'dd-mm-yyyy', 'disabled'])!!}
						
						</div><!-- /.form-group -->
						</div>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-3">
						<div class="form-group">
						{!! Form::label('horaEntrada', 'Hora Entrada:') !!}
							<div class="input-group input-append bootstrap-timepicker">
								{!! Form::text('horaEntrada',date_format(date_create($solicitud->horaEntrada),'h:i A'),['id'=>'timepicker1','class' => 'form-control timepicker ', 'disabled'])!!}
								<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
							</div>
						</div><!-- /.form-group -->
					</div><!-- /.col-sm-6 -->
							
							
					<div class="col-sm-3">
						<div class="form-group">
						{!! Form::label('horaSalida', 'Hora Salida:') !!}
							<div class="input-group input-append bootstrap-timepicker">
								{!! Form::text('horaSalida',date_format(date_create($solicitud->horaSalida),'h:i A'),['id'=>'timepicker2','class' => 'form-control timepicker', 'disabled'])!!}
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
								<textarea name="observaciones" rows="2" class="form-control" disabled>{!!$solicitud->observaciones!!}</textarea>
						</div>
					</div>
				</div>
	@if($solicitud->idEmpleadoCrea!= Auth::user()->idEmpleado)
		@if($solicitud->idEstado==1)
			@if($autorizar==1)
			  <div class="from-group">
			 	<div align="center">
			   <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />
					 <button type="submit" class="btn btn-primary btn-perspective">Autorizar</button>
					 <button type="button" id="denegarSol" class="btn btn-danger btn-perspective">Denegar</button>
			  </div>
			  </div>
			@endif
		@endif
	@endif
										
		</form>
						
	
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script>

$('#denegarSol').click(function() {
	var token =$('#token').val();
	var idSolicitud =$('#idSolicitud').val();
	 $.ajax({
			
            url:   "{{route('denegar.solicitud')}}",
            type:  'post',
			data:'idSolicitud='+idSolicitud+'&idTipo='+1+'&_token='+token,
            beforeSend: function() {
                $('body').modalmanager('loading');
            },
            success:  function (r){
                $('body').modalmanager('loading');
                if(r.status == 200){
                  console.log(r.data);
                   window.location.href = '{{route("all.permisos.unidad")}}';
                }
                else if (r.status == 400){
                    alertify.alert("Mensaje de sistema - Error",r.message);
                }else if(r.status == 401){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else{//Unknown
                    //alertify.alert("Mensaje de sistema","Este mandamiento no ha sido pagado o ya ha sido utilizado");
                    console.log(r);
                }
            },
            error: function(data){
                // Error...
                var errors = $.parseJSON(data.responseText);
                console.log(errors);
                $.each(errors, function(index, value) {
                    $.gritter.add({
                        title: 'Error',
                        text: value
                    });
                });
            }
        });
	
});


</script>
@endsection