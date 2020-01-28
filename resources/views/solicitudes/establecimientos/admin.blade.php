@extends('master')

@section('css')
<style type="text/css">
    td.details-control {
        background: url("{{ asset('/plugins/datatable/images/details_open.png') }}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url("{{ asset('/plugins/datatable/images/details_close.png') }}") no-repeat center center;
    }
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
		<strong>Algo ha salido mal.!</strong>
			{{ Session::get('msnError') }}
	</div>
@endif



    
    <div class="the-box">
	<div class="table-responsive">
	<table class="table table-striped table-hover" id="tr-soli" style="font-size:13px;" width="100%">
		<thead class="the-box dark full">
			<tr>
				<th></th>
				<th>N° Solicitud</th>
                <th>Fecha de Solicitud</th>
                <th>Fecha de Transporte</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
				<th>Lugar</th>		
                <th>Descripcion</th>
                <th>ID Estado</th>	
                <th>Estado</th>
                <th>Personas a Transportar</th>
                <th>Vehiculo Asignado</th>
                <th>Motorista</th>
                <th>Solicitado</th>
                <th>Accion</th>
                
               
			</tr>
     	</thead>
     	<tbody></tbody>
	</table>
	</div><!-- /.table-responsive -->
</div><!-- /.the-box .default -->
<!-- END DATA TABLE -->
@endsection

@section('js')
<script>

$(function(){
   var usuario = "{{$usuario}}";
   var filtro=false;
   if(usuario=='admin'){
    var filtro=true;
   }


   var table = $('#tr-soli').DataTable({
        filter: filtro,
        processing:true,
        serverSide: true,
        //ajax: "{{ route('dt.row.data.solicitudes.est') }}",
        ajax: '{!! route('dt.row.data.solicitudes.est') !!}',
        columns: [  
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
        	
            {data: 'idSolicitud', name: 'trp_solicitud.idSolicitud', searchable: false},
            {data: 'fechaCreacion', name: 'trp_solicitud.fechaCreacion', searchable: false},
            {data: 'fechaTransporte', name: 'trp_solicitud.fechaTransporte', searchable: false},
            {data: 'horaInicio', name: 'trp_solicitud.horaInicio', searchable: false},
            {data: 'horaFin', name: 'trp_solicitud.horaFin', searchable: false},
            {data: 'lugar', name: 'trp_solicitud.lugar', searchable: false},
            {data: 'descripcion', name:'trp_solicitud.descripcion', searchable: false},
            {data: 'idEstado', name:'cat_estado_solicitud.idEstado', searchable: false},
            {data: 'estado', name: 'estado',orderable: false, searchable: false},
            {data: 'Persona', name: 'Persona',orderable: false, searchable: false},
            {data: 'vehiculo', name: 'Vehiculo',orderable: false, searchable: false},
            {data: 'motorista', name:'Motorista',orderable: false, searchable: false},
            {data: 'nombresUsuario', name:'usuario.nombresUsuario'},
            {data: 'asignacion', name: 'asignacion', orderable: false, searchable: false}
                      
        ],
        
         columnDefs: [
            {
                "targets": [7,8,10,11,12,13],
                "visible": false
            }
        ],
        
        language: {
            "url": "{{ asset('plugins/datatable/lang/es.json') }}",
            'searchPlaceholder': 'por Usuario que solicito...'
        },
        order: [[1, 'desc']]
       
    });

    
    
    // Add event listener for opening and closing details
    $('#tr-soli tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
    
});
/* Formatting function for row details - modify as you need */
function format (d) {
     
    // `d` is the original data object for the row
    
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td><b>Descripcion:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.descripcion +'&nbsp;&nbsp;</td>'+
            
        '</tr>'+
        '<tr>'+
            '<td><b>Personas a Transportar:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.Persona +'&nbsp;&nbsp;</td>'+
        
        '</tr>'+

        '<tr>'+
            '<td><b>Vehiculo Asignado:<b>&nbsp;&nbsp;</td>'+
            '<td>'+  d.vehiculo +'&nbsp;&nbsp;</td>'+
            
            
        '</tr>'+
        '<tr>'+
            '<td><b>Motorista Asignado:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.motorista +'&nbsp;&nbsp;</td>'+
            
            
        '</tr>'+
        '<tr>'+
            '<td><b>Solicitad por:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombresUsuario +'&nbsp;&nbsp;</td>'+
            
            
        '</tr>'+
        
        
    '</table>';
}
       
</script>
@endsection
