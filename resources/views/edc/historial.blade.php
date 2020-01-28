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
		<strong>Auchh!</strong>
		Algo ha salido mal.	{{ Session::get('msnError') }}
	</div>
@endif

<div class="the-box">
    
    <!-- BEGIN DATA TABLE -->
	<div class="table-responsive">
	<table class="table table-striped table-hover" id="dt-edc-historial" style="font-size:13px;" width="100%">
		<thead class="the-box dark full">
			<tr>
				<th></th>
                <th>Id Evaluación</th>
				<th>Evaluación</th>
                <th>Perido</th>
                <th>Id Empleado</th>
                <th>Nombre Empleado</th>
                <th>Apellido Empleado</th>
				<th>Plaza Funcional</th>
				<th>Estado</th>
                <th>CP</th>
                <th>Totales</th>
                <th>Parciales</th>
                <th>Mínimos</th>
                <th>Evaluación</th>
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
$(document).ready(function(){
	var table = $('#dt-edc-historial').DataTable({
        serverSide: true,
        ajax: {
            url: "{{ route('dt.row.data.edc.historial') }}"
        },
        columns: [
        	{
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": ''
            },
            {data: 'idEvaluacion', name: 'idEvaluacion'},
            {data: 'nombre', name: 'nombre'},
            {data: 'periodo', name: 'periodo'},
            {data: 'idEmpleado', name: 'idEmpleado'},
            {data: 'nombresEmpleado', name: 'nombresEmpleado'},
            {data: 'apellidosEmpleado', name: 'apellidosEmpleado'},
            {data: 'nombrePlaza', name: 'nombrePlaza'},
            {data: 'nombreEstado', name: 'nombreEstado'},
            {data: 'CP', name: 'CP'},
            {data: 'sumTotales', name: 'sumTotales'},
            {data: 'sumParciales', name: 'sumParciales'},
            {data: 'sumMinimas', name: 'sumMinimas'},
            {data: 'evaluacion', name: 'evaluacion', orderable: false, searchable: false}
        ],
        columnDefs: [
            {
                "targets": [1,4,7,9,10,11,12],
                "visible": false
            }
        ],
        language: {
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
        },
        order: [[1, 'desc'],[4, 'asc']]
    });

	 // Add event listener for opening and closing details
    $('#dt-edc-historial tbody').on('click', 'td.details-control', function () {
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

function format (d) {    
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" width="100%">'+
        '<tr>'+
            '<td><b>Plaza Funcional:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombrePlaza+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Cantidad de Totales:</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.sumTotales+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Cantidad de Parciales:</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.sumParciales+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Cantidad de Mínimos:</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.sumMinimas+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Compentencia de Puesto (%):</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.CP+' %</td>'+
        '</tr>'+
    '</table>';
}
</script>
@endsection
