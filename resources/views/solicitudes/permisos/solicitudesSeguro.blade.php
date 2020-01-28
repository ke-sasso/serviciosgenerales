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
	<table class="table table-striped table-hover" id="tr-seguros" style="font-size:13px;" width="100%">
		<thead class="the-box dark full">
			<tr>
				
				<th>CORRELATIVO</th>
                <th>MOTIVO</th>
                <th>FECHA CREACION</th>
				<th>ESTADO</th>
				<th>-<th>
				
               
               
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
$( document ).ready(function() {
     
     

    var table = $('#tr-seguros').DataTable({

        processing: true,
        //serverSide: true,
        ajax: {
            url: "{{ route('dt.row.data.seguros') }}",
        },
        columns: [
			{ "data": null }, // <-- This is will your index column
            {data: 'motivo', name: 'motivo'},
            {data: 'fechaCreacion', name: 'se.fechaCreacion'},
			{data: 'nombreEstado', name: 'est.nombreEstado'},
            {data: 'detalle', name: 'detalle'}
        ],
        language: {
            "sProcessing": '<div class=\"dlgwait\"></div>',
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
            
            
        },
       "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 3, 'desc' ]]
    } );
	
	
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
});

       
</script>
@endsection
