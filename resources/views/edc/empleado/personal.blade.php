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
        @if(!empty($resultado) && $eva->activo == 1 && $resultado->aprobada == 0 && !$is_historic)
        <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Comentarios (Jefe)</label>
                        <textarea class="form-control" name="txtComentarios" rows="6" disabled="true">{{ $resultado->comentariosJefe}}</textarea>
                        <p class="help-block"></p>
                    </div>
                </div>
            </div>
            <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Compromisos laborales </label>
                        <textarea class="form-control" name="txtComentarios" rows="6" disabled="true">{{ $resultado->compromisos}}</textarea>
                        <p class="help-block">Compromisos para persona evaluada</p>
                    </div>
                </div>
            </div>

            <form id="form" method="post" action="{{ route('edc.empleado.aprobar') }}">
            <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Comentarios</label>
                        <textarea class="form-control" name="txtComentarios" rows="6"></textarea>
                        <p class="help-block">Comentario persona evaluada</p>
                    </div>
                </div>
                <div class="row text-center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="txtIdResultado" value="{{ Crypt::encrypt($resultado->idResultado) }}" />
                    @if($resultado->idEstado && $resultado->idEstado == 1)
                    <button type="submit" class="btn btn-warning btn-perspective"><i class="fa fa-check-circle"></i> Finalizar</button>
                    @endif
                </div>
            </div>
            </form>
        @elseif(!empty($resultado))
            <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Comentarios (Jefe)</label>
                        <textarea class="form-control" name="txtComentarios" rows="6" disabled="true">{{ $resultado->comentariosJefe}}</textarea>
                        <p class="help-block"></p>
                    </div>
                </div>
            </div>
            <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Compromisos laborales </label>
                        <textarea class="form-control" name="txtComentarios" rows="6" disabled="true">{{ $resultado->compromisos}}</textarea>
                        <p class="help-block">Compromisos para persona evaluada</p>
                    </div>
                </div>
            </div>

            <div class="the-box">
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12">
                        <label class="control-label"> Comentarios</label>
                        <textarea class="form-control" name="txtComentarios" rows="6" disabled="true">{{ $resultado->comentarios}}</textarea>
                        <p class="help-block">Comentario persona evaluada</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="col-sm-8">
        @if(empty($resultado) || $resultado->finalizada == 0)
            <div class="the-box full no-border">
                <div class="alert alert-warning alert-block fade in alert-dismissable">
                    <blockquote>
                    <b><p>EVALUACION DE DESEMPEÑO NO HA SIDO ENVIADA POR LA JEFATURA CORRESPONDIENTE</p>
                    </b>
                    </blockquote>
                </div>
            </div>
        @else
             <div class="the-box">
                <h4 class="small-title">RESUMEN DE RESULTADO OBTENIDO</h4>
                <div class="row">
                    <div class="form-group col-sm-6 col-xs-12 {{ $resultado->estado->claseInput }}">
                        <label class="control-label"> CP</label>
                        <input type="text" class="form-control" value="{{ $resultado->CP }} %" disabled="true">
                        <p class="help-block">Competencia en el Puesto de Trabajo (%)</p>
                    </div>
                    <div class="form-group col-sm-6 col-xs-12 {{ $resultado->estado->claseInput }}">
                        <label class="control-label"> Resultado</label>
                        <input type="text" class="form-control" value="{{ $resultado->estado->nombreEstado }}" disabled="true">
                        <p class="help-block">Resultado obtenido</p>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-th-block table-primary">
                            <thead>
                                <tr><th style="width: 15%;"></th><th style="width: 75%;">FUNCION</th><th colspan="2" style="width: 10%;">RESULTADO</th></tr>
                            </thead>
                            <tbody>
                                @foreach($resultado->funciones()->orderBy('literal','asc')->get() as $f)
                                    <tr style="background-color:#0780E8;">
                                        <td><font color="#fff">Función {{ $f->literal }}</font></td>
                                        <td colspan="2"><font color="#fff">{{ $f->nombreFuncion }}</font></td>
                                        <td><font color="#fff">{{ (empty($f->CF) || ($f->finalizada == 0))?'NE':$f->CF.'%' }}</font></td>
                                    </tr>
                                    @foreach($f->tareas()->orderBy('numero','asc')->get() as $t)
                                        <tr style="background-color:#C9DFE8">
                                            <td colspan="2">{{ $t->numero.'. - '.$t->nombreTarea }}</td>
                                            <td>{{ (empty($t->CT))?'NE':$t->CT.'%' }} </td>
                                            <td>
                                                @if($is_historic)
                                                    @if(Route::current()->getName() == 'edc.rh.admin.mostrar')
                                                        <a href="{{route('edc.rh.admin.mostrar.tarea',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>
                                                    @else
                                                        <a href="{{route('edc.historial.mostrar.tarea',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>
                                                    @endif
                                                @else
                                                    <a href="{{route('edc.empleado.evaluar.tarea.mostrar',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- END DATA TABLE -->
@if(!empty($resultado) && $resultado->finalizada == 1)<!--Solamente se muestra si esta finalizada -->
<input type="hidden" name="idEmpleado" id="idEmpleado" value="{!!$emp->idEmpleado!!}">
    <input type="hidden" name="idEvaluacion" id="idEvaluacion" value="{!!$eva->idEvaluacion!!}">
    <div class="form-group" id="Ev">
       <label class="col-md-5 control-label" for="singlebutton"></label>
       <div class="col-md-4 center-block">
              <button class="btn btn-info btn-label-left center-block" type="button" onClick="generarVistaPrevia()">
        <span><i class="fa fa-print"></i></span>
          Vista Previa
        </button>
       </div>
     </div>
     @endif
@endsection

@section('js')
<script>
function generarVistaPrevia () {
var ID_EVA = $("#idEvaluacion").val();

var ID_EMP = $("#idEmpleado").val();

  sList = window.open("{{ url('edc/empleado/vistaprevia') }}"+"/" + ID_EVA+"/" + ID_EMP, "list", "width=880,height=510");
  }
</script>
@endsection
