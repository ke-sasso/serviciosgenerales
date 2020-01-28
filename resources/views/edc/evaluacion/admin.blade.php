@extends('master')

@section('css')
<style type="text/css">
.table-responsive-2{width:100%;margin-bottom:15px;overflow-y:hidden;overflow-x:scroll;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd;-webkit-overflow-scrolling:touch}.table-responsive-2>.table{margin-bottom:0}.table-responsive-2>.table>thead>tr>th,.table-responsive-2>.table>tbody>tr>th,.table-responsive-2>.table>tfoot>tr>th,.table-responsive-2>.table>thead>tr>td,.table-responsive-2>.table>tbody>tr>td,.table-responsive-2>.table>tfoot>tr>td{white-space:normal;}.table-responsive-2>.table-bordered{border:0}.table-responsive-2>.table-bordered>thead>tr>th:first-child,.table-responsive-2>.table-bordered>tbody>tr>th:first-child,.table-responsive-2>.table-bordered>tfoot>tr>th:first-child,.table-responsive-2>.table-bordered>thead>tr>td:first-child,.table-responsive-2>.table-bordered>tbody>tr>td:first-child,.table-responsive-2>.table-bordered>tfoot>tr>td:first-child{border-left:0}.table-responsive-2>.table-bordered>thead>tr>th:last-child,.table-responsive-2>.table-bordered>tbody>tr>th:last-child,.table-responsive-2>.table-bordered>tfoot>tr>th:last-child,.table-responsive-2>.table-bordered>thead>tr>td:last-child,.table-responsive-2>.table-bordered>tbody>tr>td:last-child,.table-responsive-2>.table-bordered>tfoot>tr>td:last-child{border-right:0}.table-responsive-2>.table-bordered>tbody>tr:last-child>th,.table-responsive-2>.table-bordered>tfoot>tr:last-child>th,.table-responsive-2>.table-bordered>tbody>tr:last-child>td,.table-responsive-2>.table-bordered>tfoot>tr:last-child>td{border-bottom:0}
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
           <h4 class="small-title">DATOS EMPLEADO</h4>
           @include('edc.empleado.dataper')
        </div>

        @if($resultado->finalizada == 0)
            <div class="the-box text-center">
                <a class="btn btn-success btn-perspective" href="{{ route('edc.finalizar',['idRes' => Crypt::encrypt($resultado->idResultado)]) }}"><i class="fa fa-paper-plane"></i> Validar evaluación</a>
            </div>
        @else
             <div class="the-box text-center">
                <a class="btn btn-success btn-perspective" href="{{ route('edc.finalizar',['idRes' => Crypt::encrypt($resultado->idResultado)]) }}"><i class="fa fa-paper-plane"></i>Validar evaluación (revaluar)</a>
            </div>
            <div class="the-box">
                 <h5 class="small-title">RESUMEN DE RESULTADO OBTENIDO</h5>
                <div class="row">
                    <div class="form-group col-sm-12 col-xs-12 {{ $resultado->estado->claseInput }}">
                        <label class="control-label"> CP</label>
                        <input type="text" class="form-control" value="{{ $resultado->CP }} %" disabled="true">
                        <p class="help-block">Competencia en el Puesto de Trabajo (%)</p>
                    </div>
                    <div class="form-group col-sm-12 col-xs-12 {{ $resultado->estado->claseInput }}">
                        <label class="control-label"> Resultado</label>
                        <input type="text" class="form-control" value="{{ $resultado->estado->nombreEstado }}" disabled="true">
                        <p class="help-block">Resultado obtenido</p>
                    </div>
                </div>
            </div>
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
        @endif
        @if($resultado->aprobada == 1)
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
        <div class="table-responsive-2">
            {{-- Lista chequeo --}}
            <table class="table table-th-block" style="font-size:13px;" width="100%">
                <thead>
                    <tr>
                        <th style="width: 12%;"></th><th style="width: 68%;">TAREAS</th><th style="width: 10%;">CT</th><th style="width: 10%;">EVALUAR</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($resultado->funciones()->orderBy('literal','asc')->get() as $f)
                    <tr style="background-color:#0780E8;">
                        <td> <font color="#fff">Función {{ $f->literal }}</font></td>
                        <td colspan="2"><font color="#fff">{{ $f->nombreFuncion }}</font></td>
                        <td><font color="#fff">{{ (empty($f->CF) || ($f->finalizada == 0))?'NE':$f->CF.'%' }}</font></td>
                    </tr>
                    @foreach($f->tareas()->orderBy('numero','asc')->get() as $t)
                        <tr style="background-color:#C9DFE8">
                            <td>{{ $t->numero }}</td>
                            <td>{{ $t->nombreTarea }}</td>
                            <td>{{ (empty($t->CT))?'NE':$t->CT.'%' }} </td>
                            <td>
                              <!--  @if($resultado->aprobada==1)
                                <a href="{{route('edc.empleado.evaluar.tarea.mostrar',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>
                                @else
                                <a href="{{route('edc.empleado.evaluar.tarea',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-get-pocket"></i> Evaluar</a>
                                @endif-->

                                 <a href="{{route('edc.empleado.evaluar.tarea',['idRes' => Crypt::encrypt($resultado->idResultado), 'idTar' => Crypt::encrypt($t->idTarea) ])}}" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-get-pocket"></i> @if($resultado->aprobada==1) Revaluar @else Evaluar @endif</a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div><!-- /.table-responsive-2 -->
   <div class="rows">

        <div class="alert alert-info alert-bold-border square fade in alert-dismissable">
         <center><a href="#fakelink" class="alert-link">HABILIDADES</a><br></center>
         @if(strlen($emp->plazaFuncional->habilidades)>0)
         <?php  echo $emp->plazaFuncional->habilidades;?>
         @else
          SIN INFORMACIÓN.
         @endif
          </div>

    </div>
     <div class="rows">

        <div class="alert alert-info alert-bold-border square fade in alert-dismissable">
         <center><a href="#fakelink" class="alert-link">CONOCIMIENTOS GENERALES</a><br></center>
         @if(strlen($emp->plazaFuncional->conocimientos)>0)
         <?php  echo $emp->plazaFuncional->conocimientos;?>
         @else
          SIN INFORMACIÓN.
         @endif
          </div>

    </div>
     <div class="rows">

        <div class="alert alert-info alert-bold-border square fade in alert-dismissable">
         <center><a href="#fakelink" class="alert-link">MAQUINAS EQUIPO Y MATERIALES</a><br></center>
         @if(strlen($emp->plazaFuncional->equipoMateriales)>0)
         <?php  echo $emp->plazaFuncional->equipoMateriales;?>
         @else
          SIN INFORMACIÓN.
         @endif
          </div>

    </div>
     <div>

        <div class="rows">
            <div class="list-group info-block">
                <a href="#fakelink" class="list-group-item active"> <span class="badge badge-info">ACTITUDES</span></a>
         @if(count($emp->plazaFuncional->actitudes)>0)
            @foreach($emp->plazaFuncional->actitudes as $cono)
                <a href="#" class="list-group-item"> {{$cono->actitud->nombreTipoActitud}}</a>
            @endforeach
        @else
             <a href="#" class="list-group-item">NO EXISTEN REGISTROS</a>
        @endif
             </div>
        </div>
     </div>

    </div>



    @if($resultado->finalizada == 1)<!--Solamente se muestra si esta finalizada -->
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
    <p>&nbsp;</p>
    <input type="hidden" name="idEmpleado" id="idEmpleado" value="{!!$emp->idEmpleado!!}">
    <input type="hidden" name="idEvaluacion" id="idEvaluacion" value="{!!$eva->idEvaluacion!!}">
    <div class="form-group" id="Ev">
       <label class="col-md-5 control-label" for="singlebutton"></label>
       <div class="col-md-4 center-block">
              <button class="btn btn-info btn-label-left center-block" type="button" onClick="generarFormato()">
        <span><i class="fa fa-print"></i></span>
          Imprimir Formato
        </button>
       </div>
     </div>

</div>
<!-- END DATA TABLE -->
@endsection

@section('js')
<script>
function generarVistaPrevia ()
{
    var ID_EVA = $("#idEvaluacion").val();

    var ID_EMP = $("#idEmpleado").val();

    sList = window.open("{{ url('edc/empleado/vistaprevia') }}"+"/" + ID_EVA+"/" + ID_EMP, "list", "width=880,height=510");
}

function generarFormato()
{
    var ID_EVA = $("#idEvaluacion").val();

    var ID_EMP = $("#idEmpleado").val();

    sList = window.open("{{ url('edc/empleado/formato') }}"+"/" + ID_EVA+"/" + ID_EMP, "list", "width=880,height=510");
}
</script>
@endsection
