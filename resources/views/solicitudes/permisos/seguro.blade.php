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
		<strong>Algo ha salido mal.	</strong>{{ Session::get('msnError') }}
	</div>
@endif
<div class="alert alert-warning" role="alert">USAR FORMULARIO CUANDO SE HAGA USO DEL SEGURO M&Eacute;DICO.
</div>
<div class="the-box">

	<h4 class="small-title">REINTEGRO DE GASTOS M&Eacute;DICOS : </h4>
					
		<form  method="post" id="frmSeguro" enctype="multipart/form-data" action="{{ route('guardar.seguro') }}" autocomplete="off">
					
					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
								<label><b>SELECCIONE EL ASEGURADO:</b></label>
								<select name="presentado" id="presentado" class="form-control" required>
									<option value="0" selected></option>
									<option value="1:{{Auth::user()->idEmpleado}}">{!! $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado !!}</option>
								@if($dependientes!=null)
								
									@if(count($dependientes)>=1)
										@foreach($dependientes as $depen)
											<option value="2:{{$depen->idDependiente}}">{{$depen->nombres}}</option>
										@endforeach
									@endif
								@endif
								</select>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
								<label><b>SOLICITA REINTEGRO DE:</b></label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								
								<div class="col-sm-4 col-md-2 col-lg-2">
									{{$motivos[0]->nombre}}
								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" class="debido"  name="motivo" value="{{$motivos[0]->idMotivo}}">
								</div>
								<div class="col-sm-4 col-md-2 col-lg-2">
									{{$motivos[1]->nombre}}
								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" class="debido"  name="motivo" value="{{$motivos[1]->idMotivo}}">
								</div>
								<div class="col-sm-4 col-md-2 col-lg-2">
									{{$motivos[2]->nombre}}
								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" class="debido"  name="motivo" value="{{$motivos[2]->idMotivo}}">
								</div>
								<div class="col-sm-4 col-md-2 col-lg-2">
									{{$motivos[3]->nombre}}
								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" class="debido"  name="motivo" value="{{$motivos[3]->idMotivo}}">
								</div>
  								<br>
							</div>

							
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="form-group">
								<div class="col-sm-4 col-md-2 col-lg-2">
									{{$motivos[4]->nombre}}
								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" class="debido"  name="motivo" value="{{$motivos[4]->idMotivo}}">
								</div>
								<div class="col-sm-4 col-md-2 col-lg-2">
  									{{$motivos[5]->nombre}} 
  								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox" name="debidoA" class="motivo"  value="{{$motivos[5]->idMotivo}}" ><br>
								</div>
  								<div class="col-sm-4 col-md-2 col-lg-2">
  									{{$motivos[6]->nombre}} 
  								</div>
								<div class="col-sm-2 col-md-1 col-lg-1">
									<input type="checkbox"  class="debido"  name="motivo" value="{{$motivos[6]->idMotivo}}" >
								</div>
								
  								<br>
							</div>
	
						</div>
	
					</div>
					<br>
					
					
					<div class="row">
						<div class="col-sm-12 col-md-8 col-lg-8">
							<div class="form-group">
								<label><b>DETALLES DE LA OCURRENCIA:</b></label>
							</div>
						</div>
					</div>
					
					<div class="row">
							<div class="col-sm-4 col-md-5 col-lg-4">
							<label>1. ¿Resulta la dolencia de la ocupación del asegurado?</label>
							</div>
							<div class="col-sm-4 col-md-2 col-lg-2">
								<b>SI</b> &nbsp;&nbsp;<input type="checkbox" class="det1"  name="det1" value="1">
								&nbsp;&nbsp;
								<b>NO</b> &nbsp;&nbsp;<input type="checkbox" name="det1" class="det1"  value="0" ><br>
							</div>
					</div>
					
				<div class="row">
						<div class="col-sm-4 col-md-5 col-lg-4">
						<label>2. ¿Fue tratado anteriormente por esta dolencia?</label>
						</div>
						<div class="col-sm-4 col-md-2 col-lg-2">
							<b>SI</b> &nbsp;&nbsp;<input type="checkbox" class="det2"  name="det2" value="1">
							&nbsp;&nbsp;
							<b>NO</b> &nbsp;&nbsp;<input type="checkbox" name="det2" class="det2"  value="0" >
						</div>
				</div>
				
					
					<br>
				
						<label>SELECCIONE LOS DOCUMENTOS A PRESENTAR EN EL TRAMITE:</label>
							<div class="panel-body">
								<div class="table-responsive">
									<table width="100%" class="table table-hover table-striped" id="documentos">
									<caption class="text-danger">Recuerde estos documentos deberán ser presentados en físico a la Unidad de Recursos Humanos de esta Dirección.</caption>
										<tbody>
											<tr>
											<td>Formularios, revés y derecho, facturas, recetas, ordenes de exámenes, resultados de exámenes, Pre-autorizaciones etc.</td>
											<td width="50%"><input type="file" id="file" name="file" required></td>
											</tr>
										</tbody>
									</table>
								</div>
								
							</div>
						
					
				
					<br>
															

			  <div class="from-group">
			 	<div align="center">
			   <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
			   <button type="button" id="guardarSoli"  class="btn btn-primary btn-perspective">Guardar</button>

			  </div>
			  </div>
								  		
										
		</form>
						
	
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script>
	$(document).ready(function () {
	 			
     $('input[class="debido"]').click(function(){
	    var $inputs=$('input[class="debido"]');
	    if ($(this).is(':checked')) {
	      //console.log($('input.chkGroup').val());
	      $inputs.not(this).prop('disabled',true);
	    }
	    else{
	      $inputs.prop('disabled',false); 
	    }
  	});
	
	$('input[class="det1"]').click(function(){
	    var $inputs=$('input[class="det1"]');
	    if ($(this).is(':checked')) {
	      if($(this).val()==1){
				$('#fecha1').prop('disabled',false);
				$('#hora1').prop('disabled',false);
			}
	      $inputs.not(this).prop('disabled',true);
	    }
	    else{
		  $('#fecha1').prop('disabled',true);
		  $('#hora1').prop('disabled',true);
		  $('#hora1').val('');
		  $('#fecha1').val('');
	      $inputs.prop('disabled',false); 
	    }
  	});

      $('input[class="det2"]').click(function(){
	    var $inputs=$('input[class="det2"]');
	    if ($(this).is(':checked')) {
			if($(this).val()==1){
				$('#fecha2').prop('disabled',false);
			}
	      $inputs.not(this).prop('disabled',true);
	    }
	    else{
		 $('#fecha2').prop('disabled',true);
		 $('#fecha2').val('');
	      $inputs.prop('disabled',false); 
	    }
  	});


    $('#guardarSoli').click(function() {
   		//console.log($("#presentado option:selected" ).val())
      if($("#presentado option:selected" ).val()==0) {
        alertify.alert("Mensaje de sistema","Seleccione un asegurado.");
      }
	  else if ($('input[class="debido"]').is(':checked')==false){
		   alertify.alert("Mensaje de sistema","Marque un motivo del reintegro.");
	  }
	  else if($("#diagnostico option:selected" ).val()==0) {
        alertify.alert("Mensaje de sistema","Seleccione un diagnostico.");
      }
	  else if ($('input[class="det1"]').is(':checked')==false){
		   alertify.alert("Mensaje de sistema","Marque SI o NO, pregunta 1.");
	  }
	  else if ($('input[class="det2"]').is(':checked')==false){
		   alertify.alert("Mensaje de sistema","Marque SI o NO, pregunta 2.");
	  }
      else{
			$('#frmSeguro').submit();      		  
      }  
	});
	
	$('#capitulo').on('change', function() {
	if($("#capitulo option:selected" ).val()!=0) {
        $('#diagnostico option').each(function() {
          if ( $(this).val() != '0' ) {
              $(this).remove();
          }
        });
	var token =$('#_token').val();
	var capitulo=$("#capitulo option:selected" ).val();
	 $.ajax({
			
            url:   "{{route('get.enfermedades')}}",
            type:  'post',
			data:'capitulo='+capitulo+'&_token='+token,
            beforeSend: function() {
                $('body').modalmanager('loading');
            },
            success:  function (r){
                $('body').modalmanager('loading');
                if(r.status == 200){
                  console.log(r.data);
                   for(j=0;j<r.data.length;j++){
						$('#diagnostico').append("<option value='"+r.data[j].idEnfermedad+"'>" + r.data[j].nombreEnfermedad + "</option>");
				  }
                  $('#diagnostico').trigger('chosen:updated');
                }
                else if (r.status == 400){
                    alertify.alert("Mensaje de sistema - Error",r.message);
                }else if(r.status == 401){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else{//Unknown
                    alertify.alert("Mensaje de sistema","Este mandamiento no ha sido pagado o ya ha sido utilizado");
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
	}
	});
});

</script>
@endsection