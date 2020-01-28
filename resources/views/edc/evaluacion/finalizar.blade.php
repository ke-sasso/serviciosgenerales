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
<div class="the-box">
    <h4 class="small-title">FINALIZAR EVALUACION</h4>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a class="block-collapse collapsed" data-toggle="collapse" href="#collapse-data-emp">
                DATOS EMPLEADO
                <span class="right-content">
                    <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
                </span>
                </a>
            </h3>
        </div>
        <div id="collapse-data-emp" class="collapse" style="height: 0px;">
            <div class="panel-body" id="data-cat-est">
                {{-- COLLAPSE CONTENT --}}
                    @include('edc.empleado.dataper')
                {{-- /.COLLAPSE CONTENT --}}
            </div><!-- /.panel-body -->
        </div><!-- /.collapse in -->
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label class='text-warning'>Los campos con asterico (*) son requeridos</label>
            </div>
        </div><!--./col-sm-12-->
    </div>
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
    <h5 class="small-title">RESUMEN DE RESULTADO OBTENIDO</h5>
    <div class="table-responsive">
        <table class="table table-th-block table-primary">
            <thead>
                <tr><th style="width: 10%;"></th><th style="width: 80%;">FUNCION</th><th style="width: 10%;">RESULTADO</th></tr>
            </thead>
            <tbody>
                @foreach($resultado->funciones()->orderBy('literal','asc')->get() as $f)
                    <tr>
                        <td>Función {{ $f->literal }}</td>
                        <td>{{ $f->nombreFuncion }}</td>
                        <td>{{ (empty($f->CF) || ($f->finalizada == 0))?'NE':$f->CF.'%' }} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="form-group col-sm-6 col-xs-12 {{ $estado->claseInput }}">
            <label class="control-label"> CP</label>
            <input type="text" class="form-control" value="{{ $resultado->CP }} %" disabled="true">
            <p class="help-block">Competencia en el Puesto de Trabajo (%)</p>
        </div>
        <div class="form-group col-sm-6 col-xs-12 {{ $estado->claseInput }}">
            <label class="control-label"> Resultado</label>
            <input type="text" class="form-control" value="{{ $estado->nombreEstado }}" disabled="true">
            <p class="help-block">Resultado obtenido</p>
        </div>
    </div>

    <form id="form" method="post" action="{{ route('edc.finalizar.guardar') }}">
    <div class="row">
        <div class="form-group has-success col-sm-6 col-xs-12">
            <label>* Fecha evaluación</label>
            <input type="text" name="txtFechaEvaluacion" id="txtFechaEvaluacion" class="form-control date_masking_g" placeholder="dd-mm-yyyy" value="{{ (empty($resultado->fechaEvaluacion)||$resultado->fechaEvaluacion=='0000-00-00')?old('txtFechaEvaluacion'):date_format(date_create($resultado->fechaEvaluacion),'d-m-Y') }}">
            <p class="help-block">Fecha en la que realiza la evaluación de desempeño</p>
        </div>
    </div>
    <div class="row">
        <div class="form-group has-success col-sm-12 col-xs-12">
            <label>Comentarios</label>
            <textarea  rows="3" class="form-control" name="comentarios" id="comentarios"></textarea>
        </div>
    </div>
     <div class="row">
        <div class="form-group has-success col-sm-12 col-xs-12">
            <label>Compromiso</label>
            <textarea  rows="3" class="form-control" name="compromisos" id="compromisos"></textarea>
        </div>
    </div>




    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="txtIdResultado" value="{{ Crypt::encrypt($resultado->idResultado) }}">
            <input type="hidden" name="txtIdEstado" value="{{ Crypt::encrypt($estado->idEstado) }}">

            <a href="{{ route('edc.empleado.evaluar',['idEva' => $idEva, 'idEmp' => $idEmp]) }}" class="btn btn-warning btn-perspective"><i class="fa fa-ban"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary btn-perspective"><i class="fa fa-floppy-o"></i> Guardar</button>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    </form>
</div>
@endsection

@section('js')
<script>

</script>
@endsection
