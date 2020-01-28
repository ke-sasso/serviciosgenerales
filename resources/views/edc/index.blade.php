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
<div class="alert alert-info square fade in alert-dismissable">
    <strong>{{ $eva->nombre }} ({{ $eva->periodo }})</strong><br>
    Fecha Inicio: {{ $eva->fechaInicio }} | Fecha Fin: {{ $eva->fechaFin }}
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="the-box">
           <h4 class="small-title">DATOS PERSONALES</h4>
           @include('edc.empleado.dataper')
        </div>
    

        @if(!empty($resultado))
        <div class="the-box text-center">
            <a class="btn btn-warning btn-perspective" href="{{ route('edc.empleado.jefe',['idRes' => Crypt::encrypt($resultado->idResultado)]) }}"><i class="fa fa-user"></i> Mi evaluación</a>
        </div>
        @else 
        <div class="the-box full no-border">
            <div class="alert alert-warning alert-block fade in alert-dismissable">
                <blockquote>
                <b><p>EVALUACION DE DESEMPEÑO NO HA SIDO ENVIADO POR LA JEFATURA CORRESPONDIENTE</p>
                </b>
                </blockquote>
            </div>
        </div>
        @endif
    </div>
    <div class="col-sm-8">
        <div class="the-box">
            <h4 class="small-title">EQUIPO DE TRABAJO</h4>
            <div class="table-responsive">
            <table class="table table-striped table-hover" id="dt-emp" style="font-size:13px;" width="100%">
                <thead class="the-box dark full">
                    <tr>
                        <th></th>
                        <th>Cod. Empleado</th>
                        <th>Nombre</th>
                        <th>Género</th>
                        <th>Fecha Nacimiento</th>
                        <th>Plaza Funcional</th>
                        <th><i class="fa fa-paper-plane" aria-hidden="true"></i></th>
                        <th><i class="fa fa-eye" aria-hidden="true"></i></th>
                        <th>Evaluación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipoTrabajo as $et)
                        <tr>
                            <td></td>
                            <td>{{ $et->idEmpleado }}</td>
                            <td>{{ $et->getNombreCompleto() }}</td>
                            <td>{{ $et->getTextoGenero() }}</td>
                            <td>{{ $et->fechaNacimiento }}</td>
                            <td>{{ $et->getTextoNombrePlazaFuncional() }}</td>
                            {{--*/
                                $resultado = $et->getResultadoByIdEva($eva->idEvaluacion);
                            /*--}}
                            <td>
                                @if((!empty($resultado))?$resultado->finalizada:0)
                                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                @else
                                    <i class="fa fa-square-o" aria-hidden="true"></i>
                                @endif
                            </td><td>
                                @if((!empty($resultado))?$resultado->aprobada:0)
                                    <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                @else
                                    <i class="fa fa-square-o" aria-hidden="true"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('edc.empleado.evaluar',['idEva'=> Crypt::encrypt($eva->idEvaluacion) ,'idEmp' => Crypt::encrypt($et->idEmpleado)]) }}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-get-pocket"></i> Evaluar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
    </div>
</div>
<!-- END DATA TABLE -->
@endsection

@section('js')
<script>

$(function(){
   var table = $('#dt-emp').DataTable({
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            {data: 'codigo', name: 'codigo'},
            {data: 'nombre', name: 'nombre'},
            {data: 'genero', name: 'genero'},
            {data: 'fecha_nacimiento', name: 'fecha_nacimiento'},
            {data: 'plaza_funcional', name: 'plaza_funcional'},
            {data: 'finalizada', name: 'finalizada', orderable: false, searchable: false},
            {data: 'aprobada', name: 'aprobada', orderable: false, searchable: false},
            {data: 'evaluacion', name: 'evaluacion', orderable: false, searchable: false}
        ],
        columnDefs: [
            {
                "targets": [3,4,5,],
                "visible": false
            }
        ],
        language: {
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
        },
        order: [[1, 'asc']]
   });

   $('#dt-emp tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr); 
        if (row.child.isShown()){
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data())).show();
            tr.addClass('shown');
        }
    });
});

function format (d) {
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" width="100%">'+
        '<tr>'+
            '<td><b>Fecha Nacimiento:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.fecha_nacimiento+'</td>'+
            '<td><b>Género:<b>&nbsp;&nbsp;</td>'+
            '<td>'+ d.genero+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td colspan="2"><b>Plaza Funcional:<b>&nbsp;&nbsp;</td>'+
            '<td colspan="2">'+ d.plaza_funcional +'</td>'+
        '</tr>'+       
    '</table>';
}
       
</script>
@endsection
