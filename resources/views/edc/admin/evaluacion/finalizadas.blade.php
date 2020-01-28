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

<div class="panel panel-success">
    <div class="panel-heading" >
        <h3 class="panel-title">
            <a class="block-collapse collapsed" id='colp' data-toggle="collapse" href="#collapse-filter">
            B&uacute;squeda Avanzada de Evaluaciones finalizadas
            <span class="right-content">
                <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
            </span>
            </a>
        </h3>
    </div>



    
    <div id="collapse-filter" class="collapse" style="height: 0px;">
        <div class="panel-body " >

            {{-- COLLAPSE CONTENT --}}
            <form role="form" id="search-form">
               <div class="row">
                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                        <label>Empleado:</label>
                        <input type="text" name="empleado" id="empleado" value="" class="form-control">
                    </div>

                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                        <label>Unidad:</label>
                          <select class="form-control" name="unidad" id="unidad" >
                            <option value="" selected>Seleccione...</option>
                            @foreach($unidades as $uni)
                                <option value="{{$uni->idUnidad}}">
                                  {{$uni->nombreUnidad}}
                                </option>
                            @endforeach
                            
                         </select>
                    </div>
               </div>
               <div class="row">
                    
                     <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                     <label>Plaza Funcional:</label>
                          <select class="form-control" name="pfun" id="pfun" >
                            <option value="" selected>Seleccione...</option>
                                @foreach($plazasfun as $pfun)
                                    <option value="{{$pfun->idPlazaFuncional}}">
                                      {{$pfun->nombrePlaza}}
                                    </option>
                                @endforeach
                            </select>
                    </div>

                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                     <label>Plaza Nominal:</label>
                          <select class="form-control" name="pnom" id="pnom" >
                            <option value="" selected>Seleccione...</option>
                                @foreach($plazasnom as $pnom)
                                    <option value="{{$pnom->idPlazaNominal}}">
                                      {{$pnom->nombrePlazaNominal}}
                                    </option>
                                @endforeach
                            </select>
                    </div>
               </div>

               <div class="row">
                    
                     <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                     <label>Período de Evaluación:</label>
                          <select class="form-control" name="evaluacion" id="evaluacion" >
                            <option value="" selected>Seleccione...</option>
                                @foreach($evaluaciones as $eval)
                                    <option value="{{$eval->idEvaluacion}}">
                                      {{$eval->nombre}}
                                    </option>
                                @endforeach
                            </select>
                    </div>
               </div>
                    
                <div class="modal-footer" >
                    <div align="center">
                             <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" class="form-control"/>
                  <button type="submit" class="btn btn-success btn-perspective"><i class="fa fa-search"></i> Buscar</button>
                           </div>
                        </div>
                    
                    
            </form>
            {{-- /.COLLAPSE CONTENT --}}
        </div><!-- /.panel-body -->
    </div><!-- /.collapse in -->
</div>

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
                <th>Id Unidad</th>
                <th>Nombre Unidad</th>
                <th>Unidad</th>
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
            url: "{{ route('dt.row.data.edc.rh.admin') }}",
             data: function (d) {
                d.unidad= $('#unidad').val();
                d.empleado= $('#empleado').val();
                d.pfun= $('#pfun').val();
                d.pnom= $('#pnom').val();
                d.idEvaluacion= $('#evaluacion').val();
            }
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
            {data: 'idUnidad', name: 'idUnidad'},
            {data: 'nombreUnidad', name: 'nombreUnidad'},
            {data: 'prefijo', name: 'prefijo'},
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
                "targets": [1,3,4,5,7,10,12,13,14,15],
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

    $('#search-form').on('submit', function(e) {

        table.draw();
        e.preventDefault();
        $("#colp").attr("class", "block-collapse collapsed");
        $("#collapse-filter").attr("class", "collapse");
    });

    table.rows().remove();
    table.ajax.reload();
});

function format (d) {    
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" width="100%">'+
        '<tr>'+
            '<td><b>Evaluación:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombre+' ('+ d.periodo +')</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Unidad:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombreUnidad+'</td>'+
        '</tr>'+
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
