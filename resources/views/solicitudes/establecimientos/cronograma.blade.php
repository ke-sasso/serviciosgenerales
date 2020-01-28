@extends('master')
@section('css')

	{!! Html::style('plugins/full-calendar/css/fullcalendar.min.css') !!} 
	
	
	
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
	
	<div id="calendar"></div>

	 <div id="fullCalModal" class="modal fade">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header bg-primary">
	                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
	                <h4 id="modalTitle" class="modal-title"></h4>
	            </div>
	            <br>
	             <div class="row">
	            	<div class="col-sm-1"></div>	
	            	<div class="col-sm-8">
	            	<div class="input-group">	
	           		<div class="input-group-addon" ><b>Fecha de Transporte:</b></div>
	           		<input type="text" class="form-control" id="fechaTransporte">	
	           		</div>	
	           		</div>	
	           </div>
	           <br>	
	            <div class="row">
	           	<div class="col-sm-1"></div>
	           	<div class="col-sm-10">	
	           		<div class="input-group">
					 	<div class="input-group-addon" ><b>Lugar:</b></div>
					 	<textarea id="lugar" class="form-control" rows="2"></textarea>
						
					</div>
				</div>
	           </div>
	            
	           
	           <br>	
	           <div class="row">
	           	<div class="col-sm-1"></div>
	           	<div class="col-sm-5">	
	           		<div class="input-group">
					 	<div class="input-group-addon" ><b>Hora Inicio:</b></div>
						<input type="text" class="form-control" id="hInicio">	
					</div>
				</div>
				<div class="col-sm-5">	
	           		<div class="input-group">
					 	<div class="input-group-addon" ><b>Hora Fin:</b></div>
						<input type="text" class="form-control" id="hFin">	
					</div>
				</div>
	           </div>
	           <br>	
	            <div class="row">
	           		<div class="col-sm-1"></div>
	           		<div class="col-sm-8">	
	           			<div class="input-group">
					 	<div class="input-group-addon" ><b>Solicitado Por:</b></div>
						<input type="text" class="form-control" id="solicitado">	
						</div>
					</div>
	           </div>
	           <br>	
	           <div class="row">
	           		<div class="col-sm-1"></div>
	           		<div class="col-sm-10">	
	           			<div class="input-group">
					 	<div class="input-group-addon" ><b>Vehiculo:</b></div>
						<input type="text" class="form-control" id="vehiculo">	
						</div>
					</div>
	           </div>	
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>

@endsection

@section('js')
{!!Html::script('plugins/full-calendar/js/jquery-ui.min.js')!!}
{!! Html::script('plugins/full-calendar/js/moment.min.js') !!}
{!! Html::script('plugins/full-calendar/js/fullcalendar.min.js') !!}
{!! Html::script('plugins/full-calendar/lang/es.js') !!}

<script type="text/javascript">
	
	$('#calendar').fullCalendar({
	    header: {
	        left: 'prev,next today',
	        center: 'title',
	        right: 'basicWeek,listWeek,listDay' // buttons for switching between views
	    },

	     events: {
            url: '{{route("transporte.eventos")}}',
            type: 'get',
            error: function() {
                alert('There was an error while fetching events.');
            }
        },

        eventClick:  function(event, jsEvent, view) {
        			
       
                    $('#modalTitle').html(event.title);
                    $('#fechaTransporte').val(moment(event.start).format('MMMM DD YYYY'));
                    $('#horaInicio').val(moment(event.start).format('h:mm A'));
                    $('#horaInicio').val(moment(event.end).format('h:mm A'));
                    $('#lugar').val(event.description);
                    $('#solicitado').val(event.solicitado);
                    $('#vehiculo').val(event.vehiculo);
                    $('#hInicio').val(event.hInicio);
                    $('#hFin').val(event.hFin);
                    $('#eventInfo').html();
                    $('#fullCalModal').modal();
                    return false;
                },

	    firstDay: 1,
	   	defaultView: 'basicWeek',
	   	lang:'es',
	   	timezone:'America/El_Salvador',
   		timeFormat: 'H(:mm)',
   		

   		
});
</script>
@endsection