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
		<strong>Auchh!</strong>
		Algo ha salido mal.	{{ Session::get('msnError') }}
	</div>
@endif
<div class="alert alert-warning" role="alert">USAR FORMULARIO PARA:
	<ul>
		<li>Enfermedad: Menor o igual a 3 días, sin incapacidad médica.</li>
		<li>Incapacidad: mayor a 3 días con incapacidad médica, validad por el ISSS a partir del 4° día.</li>
	</ul>
</div>
<div class="the-box">

	<h4 class="small-title">Solicitud de Licencia: </h4>
							
		<form id="formLicencia" method="post" action="{{ route('autorizar.permiso') }}" autocomplete="off">
					<input type="hidden" name="idSolicitud" id="idSolicitud" value="{{$solicitud->idSolLicencia}}">
					<input type="hidden" name="tipoPermiso" value="2">
					<div class="row">
						<div class="col-sm-12 col-md-3 col-lg-3">
							<div class="form-group">
							<label>Fecha de Solicitud:</label>
							<input type="text" name="fechaSol" class="form-control" value="{{date('d-m-Y',strtotime($solicitud->fechaCreacion))}}" disabled>
							</div>	
						</div>	
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
							<label>NOMBRE DEL EMPLEADO (A):</label>
							<input type="text" name="nomEmpleado" class="form-control" value="{{$empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado}}" readonly>
							</div>	
						</div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<div class="form-group">
							<label>UNIDAD/DEPARTAMENTO:</label>
							<input type="text" name="unidad" class="form-control" value="{{$unidad->nombreUnidad}}" readonly>
							</div>	
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
								<label><b>SOLICITA LICENCIA </b></label>
								<!--<input type="number" name="dias" id="dias"  min="0" max="365" value="{{$solicitud->dias}}" readonly>-->
								<b>EN CONCEPTO DE:</b>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									ENFERMEDAD
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									@if($solicitud->enConcepto==2)
										<input type="checkbox" class="concepto" checked name="concepto" value="2" disabled>
									@else
										<input type="checkbox" class="concepto"  name="concepto" value="2" disabled>
									@endif
								</div>
								
								<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
  									PERSONAL
								</div>
								
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									@if($solicitud->enConcepto==4)
										<input type="checkbox" name="concepto" class="concepto" checked value="4" disabled><br>
									@else
										<input type="checkbox" name="concepto" class="concepto"  value="4" disabled><br>
									@endif
  								</div>
  								<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
  									MISI&Oacute;N OFICIAL
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">	
								@if($solicitud->enConcepto==5)
									<input type="checkbox"  class="concepto" checked name="concepto" value="5" disabled>
								@else
									<input type="checkbox"  class="concepto"   name="concepto" value="5" disabled>
								@endif
  								</div>
  								<br>
							</div>

							
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
									MATERNIDAD 
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								@if($solicitud->enConcepto==6)
									<input type="checkbox" class="concepto" checked name="concepto" value="6" disabled>
								@else
									<input type="checkbox" class="concepto"  name="concepto" value="6" disabled>
								@endif
								</div>
								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
  									DUELO
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								@if($solicitud->enConcepto==7)
									<input type="checkbox" name="concepto" class="concepto" checked value="7" disabled><br>
								@else
									<input type="checkbox" name="concepto" class="concepto"  value="7" disabled><br>
								@endif
  								</div>
  								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
  									Atencion a parientes por enfermedad Grav&iacute;sima 
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								@if($solicitud->enConcepto==8)
									<input type="checkbox"  class="concepto" checked name="concepto" value="8" disabled>
								@else
									<input type="checkbox"  class="concepto"  name="concepto" value="8" disabled>
								@endif
  								</div>
  								<br>
							</div>
	
						</div>
	
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								OTROS
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
								<input type="checkbox" name="concepto" id="otros" class="concepto" disabled value="7">
							</div>
							
							<select name="catotros" id="catotros" disabled>
								<option value="0"></option>
								@foreach($motivos as $motivo)
									@if($motivo->otro==1)
										@if($solicitud->enConcepto==$motivo->idSolMot)
										<option value="{{$motivo->idSolMot}}" selected>{!!$motivo->nombre!!}</option>
										@endif
									@endif
								@endforeach
							</select>
							</div>
							
						
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<label>OBSERVACIONES:</label>
							<input type="text" name="observacion" class="form-control" value="{{$solicitud->observaciones}}" disabled>
						</div>
					</div>
					<br>
					<!-- <div class="row">
						
							<div class="form-group">
								<div class="col-sm-12 col-md-3 col-lg-3">
								@if($solicitud->goce==1)
								<b>CON GOCE</b> &nbsp;&nbsp;<input type="checkbox" checked disabled class="motivo"  name="goce" value="1">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<b>SIN GOCE</b> &nbsp;&nbsp;<input type="checkbox" name="goce" disabled  class="motivo"  value="0" ><br>
								@else
									<b>CON GOCE</b> &nbsp;&nbsp;<input type="checkbox"  disabled class="motivo"  name="goce" value="1">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<b>SIN GOCE</b> &nbsp;&nbsp;<input type="checkbox" name="goce" checked disabled  class="motivo"  value="0" ><br>
								@endif
								</div>
								
							</div>
	
					</div>
					-->
					<br>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
						<label>EN EL PERIODO COMPRENDIDO:</label>
							<div class="row">
								<div class="col-sm-6 col-md-3 col-lg-3">
									<div class="form-group">
									<label>Fecha Inicio</label>
									{!! Form::text('fechaInicio',date_format(date_create($solicitud->fechaInicio),'d-m-Y H:i:s'),['id'=>'fechaInicio','class' => 'form-control datepicker','data-date-format'=>'yyyy-mm-dd','placeholder'=>'yyyy-mm-dd', 'disabled'])!!}
									</div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
								<div class="col-sm-6 col-md-3 col-lg-3">
									<div class="form-group">
									<label>Fecha Fin</label>
									{!! Form::text('fechaFin',date_format(date_create($solicitud->fechaFin),'d-m-Y H:i:s'),['id'=>'fechaFin','class' => 'form-control datepicker','data-date-format'=>'yyyy-mm-dd','placeholder'=>'dd-mm-yyyy', 'disabled'])!!}
									</div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
								
							</div><!-- /.row -->
						</div>
					</div>
				@if(!empty($documento))
					<label>DOCUMENTO ADJUNTO PARA LA LICENCIA:</label>
					<div class="panel-body">
						<div class="table-responsive">
							<table width="100%" class="table table-hover table-striped" id="documentos">
							<caption class="text-danger">Recuerde estos documentos deberán ser presentados en físico a la Jefatura de su Unidad.</caption>
								<tbody>
									<tr>
									<td>Incapacidades,certificado de defunción, acta de nacimiento o adopcion, carta de horarios por estudios, etc.
									<a href="{{route('ver.documento',['idDocumento' => Crypt::encrypt($documento->idDocumento)])}}" class="btn btn-xs btn btn-info btn-perspective fa fa-file" target="_blank"></a>
									</td>
									</tr>
								</tbody>
							</table>
						</div>
						
					</div>	
				@endif
@if($solicitud->idEmpleadoCrea!= Auth::user()->idEmpleado)					
	@if($solicitud->dias<=90 and $solicitud->enConcepto!=30)														
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
			data:'idSolicitud='+idSolicitud+'&idTipo='+2+'&_token='+token,
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