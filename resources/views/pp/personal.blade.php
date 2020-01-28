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

<div class="row">
    <div class="col-sm-4">
        <div class="the-box">
           <h4 class="small-title">DATOS PERSONALES</h4>
           @include('edc.empleado.dataper')
        </div>
    </div>
    <div class="col-sm-8">
             <div class="the-box">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-th-block table-primary">
                            <thead>
                                <tr><th style="width: 15%;"></th><th style="width: 75%;">FUNCION</th><th colspan="2" style="width: 10%;"></th></tr>
                            </thead>
                            <tbody>
                                @foreach($emp->plazaFuncional->funciones()->orderBy('literal','asc')->get() as $f)
                                    <tr style="background-color:#0780E8;">
                                        <td><font color="#fff">Función {{ $f->literal }}</font></td>
                                        <td colspan="3"><font color="#fff">{{ $f->nombreFuncion }}</font></td>
                                    
                                    </tr>
                                    @foreach($f->tareas()->orderBy('numero','asc')->get() as $t)
                                        <tr style="background-color:#C9DFE8">
                                            <td colspan="3">{{ $t->numero.'. - '.$t->nombreTarea }}</td>
                                            <td>
                                             <a href="{{route('perfiles.puesto.emp.showTar',['idEmp' => Crypt::encrypt($emp->idEmpleado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
    </div>
</div>
<!-- END DATA TABLE -->
@endsection

@section('js')
@endsection
