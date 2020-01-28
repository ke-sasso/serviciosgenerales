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
							<h4 class="small-title">Nueva Solicitud de Transporte</h4>
							
							<form id="form" method="post" action="{{ route('solicitudes.est.confirmaciones.guardar') }}" autocomplete="off">
									<div class="row">
									<!--FECHA DE SOLICITUD DESDE-->
									<div class="col-sm-3">
										<div class="hero-unit">
										<div class="form-group">
										{!! Form::label('fechaSolicitudD', 'Fecha de Transporte:') !!}
										{!! Form::text('fechaSolicitudD',null,['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy', 'required'])!!}
										
										</div><!-- /.form-group -->
										</div>
									</div><!-- /.col-sm-6 -->
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('horaInicio', 'Hora Inicio:') !!}
											<div class="input-group input-append bootstrap-timepicker">
												{!! Form::text('horaInicio',null,['id'=>'timepicker1','class' => 'form-control timepicker ', 'required'])!!}
												<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
											</div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									
									
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('horaFin', 'Hora Fin:') !!}
											<div class="input-group input-append bootstrap-timepicker">
												{!! Form::text('horaFin',null,['id'=>'timepicker2','class' => 'form-control timepicker'])!!}
												<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
											</div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									
									<!--/FECHA DE SOLICITUD DESDE-->
    
									<div class="col-sm-2">
										<div class="form-group">
										{!! Form::label('ConMotorista', 'Con Motorista:') !!}
											<select class="form-control" name="conMotorista" id="conMotorista" required>
												<option value="1">SI</option>
												<option value="0">NO</option>
												
											</select>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
										
								</div>
								  	<div class="form-group">
											{!! Form::label('lugar', 'Lugar (Direccion):') !!}
											{!! Form::text('lugar',null,['id'=>'lugar','class' => 'form-control', 'required'])!!}
									</div>

								
                <div class="form-group">
                    {!! Form::label('persona', 'Personas a Transportar:') !!}
                  <div class="multi-field-wrapper">
                      <button type="button" class="add-field">Agregar mas Personas</button>        
                      <div class="multi-fields">
                        <div class="row">
                        <div class="col-sm-6 col-md-6">
                          <div class="multi-field">
                             <select class="form-control" name="idEmpleado[]" id="selectemple" required>
                                    <option value="" disabled selected>Seleccione un Empleado
                                @foreach($empleados as $empleado)
                                    <option value="{{$empleado->idEmpleado}}">{{$empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado}}</option>
                                @endforeach
                             </select>
          
                           </div>
                        </div>
                        </div>
                    </div>
                  </div>
								</div>

                 
									<div class="form-group">
											{!! Form::label('descripcion', 'Descripcion:') !!}
											{!! Form::text('descripcion',null,['class' => 'form-control'])!!}
											
									</div>

																		

										  <div class="from-group">
										 
										   <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                           					 <button type="submit" class="btn btn-primary btn-perspective">Registrar <i class="fa fa-plus"></i></button>
										  </div>
								  		
										
						</form>
						
	
</div>
@endsection
@section('js')
{!!Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js')!!}
<script type="text/javascript">
	 $(document).ready(function () {
	 			
               var date = new Date();
               date.setDate(date.getDate());
               var currentDate = new Date();
               currentDate.setDate(date.getDate()+1);
               console.log(currentDate);
           //   $('#fechaSolicitudD').datepicker("setDate",currentDate);
				//var myDate = date.addDays(1);
               $('#fechaSolicitudD').datepicker({ 
                    startDate: date,
                    setDate:currentDate,
                    autoclose:true
			    });

			    $('#timepicker1').timepicker({
           			minuteStep:5
				  });
			   

               	$('#timepicker2').timepicker({
				   		defaultTime:false,
				   		minuteStep:5
				   	});

     $('#form').submit(function(event) {
     		
     	$.ajax({
            data:  $('#form').serialize(),
            url:   "{{ route('solicitudes.est.confirmaciones.guardar') }}",
            type:  'post',
            dataType: "json",
            beforeSend: function() {
                $('body').modalmanager('loading');
            },
            success:  function (r){
                $('body').modalmanager('loading');
                if(r.status == 200){
                  if(r.data==='0'){
                	  alertify.alert("Mensaje de sistema",r.message);
                  	window.location.href = '{{route("solicitudes.est")}}';
                  }
                  else {
                   alertify.alert("Mensaje de sistema",r.message);
                	 alertify.confirm('Nueva Solicitud', 'Desea agregar una nueva Solicitud de Transporte para su regreso?', 
     					      function(){ alertify.success('SI'), window.location.href = '{{route("nuevasolicitud")}}';  
     						     }, function(){ alertify.error('NO'),window.location.href = '{{route("solicitudes.est")}}';});
                  }
                }
                else if (r.status == 400){
                    alertify.alert("Mensaje de sistema - Error",r.message);
                }else if(r.status == 401){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else{//Unknown
                    alertify.alert("Mensaje de sistema - Error", "Oops!. Algo ha salido mal, contactar con el adminsitrador del sistema para poder continuar!");
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
        return false;

     	
    });
    
        
        /* ADD DESTINATION */
        $('.multi-field-wrapper').each(function() {
          var $wrapper = $('.multi-fields', this);
          var x = 1;
          $(".add-field", $(this)).click(function(e) {
            x++;
              $($wrapper).append('<div class="row"><div class="col-sm-6 col-md-6"><div class="multi-field"><select class="form-control" name="idEmpleado[]" id="selectemple" required><option value="" disabled selected>  Seleccione un Empleado @foreach($empleados as $empleado)<option value="{{$empleado->idEmpleado}}">{{$empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado}}</option> @endforeach</select><a href="#" class="remove_field">Eliminar</a></div></div></div>');
          });
          
          $($wrapper).on("click",".remove_field", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
          })
        });
    
    
        
});
	

	function validarFecha() {
        var inicio = document.getElementById('fechaSolicitudD').value; 
        var finalq  = document.getElementById('fechaSolicitudH').value;
        var f = new Date();
        inicio= new Date(inicio);
        finalq= new Date(finalq);
        if(inicio< f.getDate())
          alert('La fecha de Solicitud Desde no puede ser menor que la fecha actual');
        	if(finalq< inicio)
        		alert('La fecha de Solicitud Hasta no puede ser menor que la fecha de Solicitud Desde');
        };


$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><a href="#" class="remove_field">Eliminar</a></div>'); //add input box

        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    });
});

</script>


@endsection