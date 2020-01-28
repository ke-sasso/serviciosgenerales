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
<div class="the-box panel panel-primary">

	<h4 class="small-title">Nueva Solicitud de Licencia: </h4>
							
		<form id="formLicencia" method="post" action="{{ route('guardar.licencia') }}" enctype="multipart/form-data" autocomplete="off">

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
							<label>NOMBRE DEL EMPLEADO (A):</label>
							<input type="text" name="nomEmpleado" class="form-control" value="{!! $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado !!}" readonly>
							</div>	
						</div>
						<div class="col-sm-6 col-md-4 col-lg-4">
							<div class="form-group">
							<label>UNIDAD/DEPARTAMENTO:</label>
							<input type="text" name="unidad" class="form-control" value="{{$empleado->nombreUnidad}}" readonly>
							</div>	
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
								<label><b>SOLICITA LICENCIA POR:</b></label>
								<input type="number" name="dias" id="dias"  min="0" max="365" value="" readonly>
								<b> EN CONCEPTO DE:</b>
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
									<input type="checkbox" class="concepto"  name="concepto" value="2">
								</div>
								
								<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
  									PERSONAL
								</div>
								
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">	
									<input type="checkbox" name="concepto" class="concepto"  value="4" ><br>
  								</div>
  								<div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
  									MISI&Oacute;N OFICIAL
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">	
									<input type="checkbox"  class="concepto"  name="concepto" value="5" >
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
									<input type="checkbox" class="concepto"  name="concepto" value="6">
								</div>
								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
  									DUELO
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<input type="checkbox" name="concepto" class="concepto"  value="7" ><br>
  								</div>
  								<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
  									Atencion a parientes por enfermedad Grav&iacute;sima 
								</div>
								<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
									<input type="checkbox"  class="concepto"  name="concepto" value="8" >
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
								<input type="checkbox" name="concepto" id="otros" class="concepto" value="0">
							</div>
							
							<select name="catotros"  id="catotros" disabled>
								<option value="0"></option>
								@foreach($motivos as $motivo)
									@if($motivo->otro==1)
										<option value="{{$motivo->idSolMot}}">{!!$motivo->nombre!!}</option>
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
							<input type="text" name="observacion" class="form-control" value="">
						</div>
					</div>
					<br>
					
					<br>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<label>EN EL PERIODO COMPRENDIDO</label>
								<div class="row">
									<div class="form-group">
										<div class="col-sm-6 col-md-2 col-lg-2">									
											<div class="input-group">
												<div class="input-group-addon">Del:</div>
												<input type="text" class="form-control datepicker" id="fechaInicio" name="fechaInicio" placeholder="dd-mm-yyyy">
											</div>
										</div><!-- /.col-sm-6 -->
										<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
											<div class="input-group">
												<div class="input-group-addon">Hora</div>
												<input type="text" class="form-control " id="fechaInicioH" name="fechaInicioH">
											</div>
										</div>
										
										<div class="col-sm-6 col-md-2 col-lg-2">									
											<div class="input-group">
												<div class="input-group-addon">Al:</div>
												<input type="text" class="form-control datepicker" id="fechaFin" name="fechaFin" placeholder="dd-mm-yyyy">
											</div>
										</div><!-- /.col-sm-6 -->
										<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
											<div class="input-group">
												<div class="input-group-addon">Hora</div>
												<input type="text" class="form-control " id="fechaFinH" name="fechaFinH">
											</div>
										</div>										
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<button  type="button" id="validar" class="btn btn-primary">Validar</i></button>
										</div>
									</div>
								</div><!-- /.row -->								
						</div>						
					</div>
			  <br>					
			  <label>DOCUMENTO ADJUNTO PARA LA LICENCIA:</label>
							<div class="panel-body">
								<div class="table-responsive">
									<table width="100%" class="table table-hover table-striped" id="documentos">
									<caption class="text-danger">Recuerde estos documentos deberán ser presentados en físico a la Jefatura de su Unidad.</caption>
										<tbody>
											<tr>
											<td>Incapacidades,certificado de defunción, acta de nacimiento o adopcion, carta de horarios por estudios, etc.</td>
											<td width="50%"><input type="file" id="file" name="file"></td>
											</tr>
										</tbody>
									</table>
								</div>
								
							</div>												

			  <div id="guardar" class="from-group">
			 	<div align="center">
			   <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}" />
					 <button type="submit" class="btn btn-primary">Guardar</button>
			  </div>
			  </div>
								  		
										
		</form>
						
	
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script>
	var concepto;
	var goce;
	

	$(document).ready(function () {
		
		
		//$('#guardar').hide();
	 	var date = new Date();
         date.setDate(date.getDate());
         var currentDate = new Date();
		
		
	  $('#fechaInicio').datepicker({ 
                    startDate: date,
                    setDate:currentDate,
                    autoclose:true
		});

	  $('#fechaFin').datepicker({ 
                    startDate: date,
                    setDate:currentDate,
                    autoclose:true
		});

		$('#fechaInicioH').timepicker({
		    minuteStep: 1,
		    showInputs: false,
		    disableFocus: true
		});

		$('#fechaFinH').timepicker({
		    minuteStep: 1,
		    showInputs: false,
		    disableFocus: true
		});	  
	
	 $('#fechaInicio').val((currentDate.getMonth()+1)+"/"+currentDate.getDate()+"/"+currentDate.getFullYear());
	 $('#fechaFin').val((currentDate.getMonth()+1)+"/"+currentDate.getDate()+"/"+currentDate.getFullYear());
			
     $('input[class="concepto"]').click(function(){
	    var $inputs=$('input[class="concepto"]');
	    if ($(this).is(':checked')) {
	    	if($(this).val()==0){
				$('#catotros').on('change', function() {
				  if(this.value==0){
					//concepto=$("#catotros option:selected" ).val();  
				  }
				  else{
					concepto=$("#catotros option:selected" ).val();
				  }
				});
	    		$('#catotros').prop('disabled', false);
	    	}
	    	else{
				concepto=$(this).val();
	    		$('#catotros').prop('disabled', true);
	    		$("#catotros").val("0").change();
	    	}
		  
	      $inputs.not(this).prop('disabled',true);
		  $('#guardar').show();
	    }
	    else{
	      $inputs.prop('disabled',false); 
	    }
  	});

     $('input[class="motivo"]').click(function(){
	    var $inputs=$('input[class="motivo"]');
	    if ($(this).is(':checked')) {
	      //console.log($('input.chkGroup').val());
		  goce=$(this).val();
	      $inputs.not(this).prop('disabled',true);
	    }
	    else{
	      $inputs.prop('disabled',false); 
	    }
  	});
	
	

});

// funcion para validar la fecha 
	$('#validar').click(function(event){
		console.log(concepto);
		console.log(typeof(concepto));
		if(typeof(concepto)!="undefined"){
		 
			  var fechaInicio = $('#fechaInicio').val()+" "+$('#fechaInicioH').val();
			  var fechaFin = $('#fechaFin').val()+" "+$('#fechaFinH').val();
			  var token =$('#token').val();
			  console.log(new Date(fechaInicio));
			  console.log(new Date(fechaFin));
			  console.log(token);
			 $.ajax({
				data:'fechaInicio='+fechaInicio+'&fechaFin='+fechaFin+'&concepto='+concepto+'&goce='+goce+'&_token='+token,
				url:   "{{route('calcular.dias')}}",
				type:  'post',
			   
				beforeSend: function() {
					$('body').modalmanager('loading');
				},
				success:  function (r){
					$('body').modalmanager('loading');
					console.log(r);
					if(r.status == 200){
					  $('#guardar').show();
					  //console.log(validado);
						document.getElementById('dias').value=r.data;
					}
					else if (r.status == 400){
						$('#guardar').hide();
						alertify.alert("Mensaje de sistema - Error",r.message);
					}else if(r.status == 401){
						alertify.alert("Mensaje de sistema",r.message, function(){
							window.location.href = r.redirect;
						});
					}else{//Unknown
						alertify.alert("Mensaje de sistema","No se han podido validar las fechas");
						//console.log(r);
					}
				},
				error: function(data){
					// Error...
					var errors = $.parseJSON(data.responseText);
				   // console.log(errors);
					$.each(errors, function(index, value) {
						$.gritter.add({
							title: 'Error',
							text: value
						});
					});
				}
			});
		  	 
		}
		else{
			alertify.alert("Mensaje de sistema","Debe seleccionar el motivo antes de calcular los dias.");
		}
});





</script>
@endsection