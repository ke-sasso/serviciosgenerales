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
<div class="panel panel-success">
    <div class="panel-heading" >
        <h3 class="panel-title">
            <a class="block-collapse collapsed" id='colp' data-toggle="collapse" href="#collapse-filter">
            B&uacute;squeda Avanzada de Desempeños
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
               		<div class="form-group col-sm-6 col-xs-6 col-md-12 col-lg-12">
                      <label>UNIDAD:</label>
                      <select class="form-control chosen-select" multiple name="unidad" id="unidad" >
                            <option value="">Seleccione una...</option>
                            @if(!empty($unidades))
                               @foreach($unidades as $unidad)
                                  <option value="{{$unidad->idUnidad}}">{{$unidad->nombreUnidad}}</option>
                               @endforeach
                            @endif
                      </select>
                  </div>
                  <div class="form-group col-sm-6 col-xs-6 col-md-12 col-lg-12">
                      <label>PLAZA:</label>
                      <select class="form-control" name="plazaFuncional"  multiple id="plazaFuncional" >
                            <option value="">Seleccione una...</option>
                            @if(!empty($funcionales))
                               @foreach($funcionales as $funcional)
                                  <option value="{{$funcional->idPlazaFuncional}}">{{$funcional->nombrePlaza}}</option>
                               @endforeach
                            @endif
                      </select>
                  </div>

               </div>
               <div class="row">
                  <div class="form-group col-sm-6 col-xs-6 col-md-4 col-lg-4">
                      <label>ESTADO:</label>
                      <select class="form-control" name="estado" id="estado" >
                            <option value="">Seleccione una...</option>
                            @if(!empty($estados))
                               @foreach($estados as $estado)
                                  <option value="{{$estado->idEstado}}">{{$estado->nombreEstado}}</option>
                               @endforeach
                            @endif
                      </select>
                  </div>

                  <div class="form-group col-sm-6 col-xs-6 col-md-4 col-lg-4">
                      <label>EVALUACIONES:</label>
                      <select class="form-control" name="estado" id="estado" >
                            <option value="">Seleccione una...</option>
                            @if(!empty($evaluaciones))
                               @foreach($evaluaciones as $eva)
                                  <option value="{{$eva->idEvaluacion}}">{{$eva->nombre}} ({{$eva->periodo}})</option>
                               @endforeach
                            @endif
                      </select>
                  </div>

               </div>

               </div>

                <div class="modal-footer" >
                	<div align="center">
					         <input type="hidden" name="_token" value="{{ csrf_token() }}" class="form-control"/>
                  <button type="submit" class="btn btn-success btn-perspective"><i class="fa fa-search"></i> Buscar</button>
					       </div>
				        </div>


            </form>
            {{-- /.COLLAPSE CONTENT --}}
        </div><!-- /.panel-body -->
    </div><!-- /.collapse in -->
</div>


<div class="row">
  <div class="the-box">
	<div class="table-responsive">
	<table class="table table-striped table-hover" id="dt-desempenios" style="font-size:13px;" width="100%">
		<thead class="the-box dark full">
			<tr>
        <th>-</th>
        <th>DESEMPEÑO</th>
        <th>EMPLEADO</th>
        <th>PLAZA FUNCIONAL</th>
  			<th>UNIDAD</th>
  			<th>ESTADO</th>
        <th>EVALUACIÓN</th>
        <th>Función</th>
        <th>Tarea</th>
        <th>Accion</th>
        <th><input type="checkbox" name="allResultados" id="allResultados" class="checkAll"></th>
			</tr>
     	</thead>
     	<tbody></tbody>
	</table>
	</div><!-- /.table-responsive -->
</div><!-- /.the-box .default -->
</div>
<!-- END DATA TABLE -->
@endsection

@section('js')
<script>
$( document ).ready(function() {
   $("#unidad").chosen({width: "inherit"});
   $("#plazaFuncional").chosen({width: "inherit"});

  var table = $('#dt-desempenios').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: "{{ route('dt.row.data.rh.capacitaciones.productos') }}",
             data: function (d) {
                 d.plazaFuncional = $('#plazaFuncional').val();
                 d.unidad = $('#unidad').val();
                 d.estado=$('#estado').val();
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
            {data: 'nombreDesempenio', name: 'nombreDesempenio'},
            {data: 'nomEmpleado', name: 'nomEmpleado'},
            {data: 'nombrePlaza', name: 'nombrePlaza'},
            {data: 'nombreUnidad', name: 'nombreUnidad'},
            {data: 'nombreEstado', name: 'nombreEstado'},
            {data: 'nombre', name: 'nombre'},
            {data: 'nombreFuncion', name: 'nombreFuncion'},
            {data: 'nombreTarea', name: 'nombreTarea'},
            {data: 'accionTomar', name: 'accionTomar'},
            {data: 'opcion', name: 'opcion', orderable: false, searchable: false}
        ],
        columnDefs: [
            {
                
                "targets": [3,4,7,8,9],
                "visible": false
            
            }
        ],
        language: {
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
        },
    });

$('#search-form').on('submit', function(e) {

        table.draw();
        e.preventDefault();
        $("#colp").attr("class", "block-collapse collapsed");
        $("#collapse-filter").attr("class", "collapse");
    });

 table.rows().remove();

 $('#allResultados').change(function(){
    var cells = table.cells( ).nodes();
    $( cells ).find(':checkbox').prop('checked', $(this).is(':checked'));
});
   // Add event listener for opening and closing details
    $('#dt-desempenios tbody').on('click', 'td.details-control', function () {
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
            '<td><b>Unidad:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombreUnidad+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Plaza Funcional:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombrePlaza+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Función:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.nombreFuncion+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Tarea:</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.nombreTarea+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td><b>Accion a tomar:</b>&nbsp;&nbsp;</td>'+
            '<td>'+d.accionTomar+'</td>'+
        '</tr>'+
    '</table>';
}







   // }); //en Datatable


</script>
@endsection
