@extends('master')

@section('css')
	{!! Html::style('plugins/full-calendar/css/fullcalendar.min.css') !!} 

<style>
	
</style>
@endsection

@section('contenido')

<div class="the-box">
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
			url: '{{route("get.marcaciones")}}',
            type: 'get',
            error: function() {
                alert('There was an error while fetching events.');
            }
		}
   		});

   		
//});
</script>

</script>
@endsection

