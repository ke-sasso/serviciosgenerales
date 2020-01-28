@extends('master')

@section('css')
	{!! Html::style('plugins/full-calendar/css/fullcalendar.min.css') !!} 

<style>
	
</style>
@endsection

@section('contenido')

<div class="the-box">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		    <div class="form-group">
				<label>Seleccione el empleado</label>
				<select class="form-control" id="idEmpleado">
					<option value="0">Seleccione...</option>
					@foreach($empleados as $emp)
						<option value="{{$emp->idEmpleado}}">{{$emp->nombresEmpleado.' '.$emp->apellidosEmpleado}}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
	<br>
	<div id='calendar'></div>
</div>
@endsection

@section('js')
{!!Html::script('plugins/full-calendar/js/jquery-ui.min.js')!!}
{!! Html::script('plugins/full-calendar/js/moment.min.js') !!}
{!! Html::script('plugins/full-calendar/js/fullcalendar.min.js') !!}
{!! Html::script('plugins/full-calendar/lang/es.js') !!}
<script type="text/javascript">

	var events;	
	var token =$('#_token').val();
	var fechaDesde=$("#fechaDesde").text();
	var fechaHasta=$("#fechaHasta").val();

$("#idEmpleado").on('change', function() {
	var idEmpleado=this.value;
	$('#calendar').fullCalendar('destroy');
	$('#calendar').fullCalendar({
	    header: {
	        left: 'prev,next today',
	        center: 'title',
	        right: 'month' // buttons for switching between views
	    },
	    navLinks: true, // can click day/week names to navigate views
		businessHours: true, // display business hours
		editable: true,
		displayEventTime : false,
		events: {
			url: '{{route("get.marcaciones.by.empleado")}}',
            type: 'POST',
            data: {
            	idEmp: idEmpleado,
            	_token:"{{ csrf_token() }}"
        	},
            error: function() {
                alert('There was an error while fetching events.');
            }
		}
   		});
	$('#calendar').fullCalendar('rerenderEvents'); 
});

   		
//});
</script>

</script>
@endsection

