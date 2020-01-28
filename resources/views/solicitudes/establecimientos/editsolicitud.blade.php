@extends('master')

@section('css')
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
<div class="the-box">
							<h4 class="small-title">Edicion Solicitud de Transporte</h4>
							
						{!!Form::open(['route' => ['transporte.updatesolicitud'], 'method' => 'POST'])!!}
									
									<div class="form-group">
										{!! Form::hidden('idSolicitud', Crypt::encrypt($trpsolicitud->idSolicitud)) !!}
									</div>
									
									<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('fechaSolicitudD', 'Fecha de Solicitud Desde:') !!}
										{!! Form::text('fechaSolicitudD',date_format(date_create($trpsolicitud->fechaSolicitudDesde),'d-m-Y'),['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy', 'required'])!!}


										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('horaInicio', 'Hora Inicio') !!}
											<div class="input-group input-append bootstrap-timepicker">
												{!! Form::text('horaInicio',date_format(date_create($trpsolicitud->fechaSolicitudDesde),'h:i A'),['id'=>'timepicker1','class' => 'form-control timepicker ', 'required'])!!}
												<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
											</div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('fechaSolicitudH', 'Fecha de Solicitud Hasta:') !!}
										{!! Form::text('fechaSolicitudH',date_format(date_create($trpsolicitud->fechaSolicitudHasta),'d-m-Y'),['id'=>'fechaSolicitudH','class' => 'form-control datepicker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy', 'required'])!!}
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<div class="col-sm-3">
										<div class="form-group">
										{!! Form::label('horaFin', 'Hora Fin') !!}
											<div class="input-group input-append bootstrap-timepicker">
												{!! Form::text('horaFin',date_format(date_create($trpsolicitud->fechaSolicitudHasta),'h:i A'),['id'=>'timepicker2','class' => 'form-control timepicker ', 'required'])!!}
												<span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
											</div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									
									
    
										
								</div>
								  	<div class="form-group">
											{!! Form::label('lugar', 'Lugar') !!}
											{!! Form::text('lugar',$trpsolicitud->lugar,['class' => 'form-control text-uppercase', 'required'])!!}
									</div>

									<div class="form-group">
											{!! Form::label('persona', 'Personas a Transportar') !!}
										<div class="input_fields_wrap">
											
    										
    										<button class="add_field_button">Agregar mas Personas</button>
    										@foreach($detallepersonas as $personas)
    											<div>
    											<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}" />
    											<input type="hidden" id="iddetallep" name="idDetalleP[]" value="{{$personas->idDetalleP}}" class="form-control">
    												
    											<input type="text" id="persona" name="mytext[]" value="{{$personas->persona}}" class="form-control text-uppercase" required>
    											<a href="#" class="remove_field" data-confirm="Esta seguro de eliminar esta persona?" data="{{$personas->idDetalleP}}">Eliminar Persona</a>
    											
    											</div>
    										@endforeach
    										

											
										</div>
									</div>
									
									<div class="form-group">
											{!! Form::label('descripcion', 'Descripcion') !!}
											
											{!! Form::text('descripcion',$trpsolicitud->descripcion,['class' => 'form-control'])!!}
											
									</div>

									<div class="form-group">
											{!! Form::label('estado', 'Estado') !!}
											
											{!! Form::select('catestado',$catestado,['class' => 'form-control' ,'required'])!!}
											
									</div>


										  <div class="from-group">
										  {!!Form::submit('Guardar', ['class' => 'btn btn-primary'])!!}
										  </div>
								  		
										
										
										
												
	

										
											
																		  
						{!!Form::close()!!}
						
	
</div>
@endsection
@section('js')
<script type="text/javascript">
	$(document).ready(function () {
                var date = new Date();
              	//var options = { day: "numeric", month: "numeric",year: "numeric"};        
        		//date.toLocaleDateString( date.getTimezoneOffset(), options);
                date.setDate(date.getDate());
               
                //console.log(date);
               $('#fechaSolicitudD').datepicker({ 
                    startDate: date
                });
               $('#fechaSolicitudH').datepicker({ 
                    startDate: date
                });
        });

	$('#timepicker1').timepicker({minuteStep:1});
	$('#timepicker2').timepicker({minuteStep:1});
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = {{count($detallepersonas)}}; //initlal text box count
    
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><input type="text" name="mytext[]" class="form-control text-uppercase" required/><a href="#" class="remove_field">Eliminar</a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); 
        var choice = confirm(this.getAttribute('data-confirm'));
        var id=(this.getAttribute('data'));
        if(choice){
      	
      	//console.log({{$personas->idDetalleP}});
        $(this).parent('div').remove(); x--;
         $.ajax({
         	//URL::route
		      url: "{{route('detallep.destroy')}}",
		      type: 'post',
		      data: {'iddetalle':id, '_token': $('#token').val()},
		     
		     
		    }); 

    	}
    })
});

</script>

@endsection