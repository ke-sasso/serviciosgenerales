@extends('master')

@section('css')
<style type="text/css">
.table-responsive-2{width:100%;margin-bottom:15px;overflow-y:hidden;overflow-x:scroll;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd;-webkit-overflow-scrolling:touch}.table-responsive-2>.table{margin-bottom:0}.table-responsive-2>.table>thead>tr>th,.table-responsive-2>.table>tbody>tr>th,.table-responsive-2>.table>tfoot>tr>th,.table-responsive-2>.table>thead>tr>td,.table-responsive-2>.table>tbody>tr>td,.table-responsive-2>.table>tfoot>tr>td{white-space:normal;}.table-responsive-2>.table-bordered{border:0}.table-responsive-2>.table-bordered>thead>tr>th:first-child,.table-responsive-2>.table-bordered>tbody>tr>th:first-child,.table-responsive-2>.table-bordered>tfoot>tr>th:first-child,.table-responsive-2>.table-bordered>thead>tr>td:first-child,.table-responsive-2>.table-bordered>tbody>tr>td:first-child,.table-responsive-2>.table-bordered>tfoot>tr>td:first-child{border-left:0}.table-responsive-2>.table-bordered>thead>tr>th:last-child,.table-responsive-2>.table-bordered>tbody>tr>th:last-child,.table-responsive-2>.table-bordered>tfoot>tr>th:last-child,.table-responsive-2>.table-bordered>thead>tr>td:last-child,.table-responsive-2>.table-bordered>tbody>tr>td:last-child,.table-responsive-2>.table-bordered>tfoot>tr>td:last-child{border-right:0}.table-responsive-2>.table-bordered>tbody>tr:last-child>th,.table-responsive-2>.table-bordered>tfoot>tr:last-child>th,.table-responsive-2>.table-bordered>tbody>tr:last-child>td,.table-responsive-2>.table-bordered>tfoot>tr:last-child>td{border-bottom:0}

.star-cb-group {
  /* remove inline-block whitespace */
  font-size: 0;
  /* flip the order so we can use the + and ~ combinators */
  unicode-bidi: bidi-override;
  direction: rtl;
  /* the hidden clearer */
}
.star-cb-group * {
  font-size: 1rem;
}
.star-cb-group > input {
  display: none;
}
.star-cb-group > input + label {
  /* only enough room for the star */
  display: inline-block;
  overflow: hidden;
  text-indent: 9999px;
  width: 1em;
  white-space: nowrap;
  cursor: pointer;
}
.star-cb-group > input + label:before {
  display: inline-block;
  text-indent: -9999px;
  content: "☆";
  color: #5E665C;
}
.star-cb-group > input:checked ~ label:before, .star-cb-group > input + label:hover ~ label:before, .star-cb-group > input + label:hover:before {
  content: "★";
  color: #F7E304;
  text-shadow: 0 0 1px #333;
}
.star-cb-group1 > .star-cb-clear + label {
  text-indent: -9999px;
  width: .5em;
  margin-left: -.5em;
}
.star-cb-group > .star-cb-clear + label:before {
  width: .5em;
}
.star-cb-group:hover > input + label:before {
  content: "☆";
  color: #5E665C;
  text-shadow: none;
}
.star-cb-group:hover > input + label:hover ~ label:before, .star-cb-group:hover > input + label:hover:before {
  content: "★";
  color: #F7E304;
  text-shadow: 0 0 1px #333;
}

:root {
  font-size: 1.8em;
  font-family: Helvetica, arial, sans-serif;
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
    <h4 class="small-title">EVALUACION DE TAREAS</h4>

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
         <div class="form-group col-sm-6 col-xs-12">
            <label>Función {{ $reTar->funcion->literal }}</label>
            <textarea class="form-control" readonly="true" rows="4">{{ $reTar->funcion->nombreFuncion }}</textarea>
        </div>
        <div class="form-group col-sm-6 col-xs-12">
            <label>Tarea {{ $reTar->tarea->numero }}</label>
            <textarea class="form-control" readonly="true" rows="4">{{ $reTar->tarea->nombreTarea }}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label class='text-warning'>Los campos con asterico (*) son requeridos</label>
            </div>
        </div><!--./col-sm-12-->
    </div>

    <form id="form" method="post" action="{{ route('edc.empleado.evaluar.tarea.actualizar') }}">
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">CRITERIO DE DESEMPEÑO</th><th style="width: 20%;">(*) NIVEL COMPETENCIA</th><th style="width: 20%;">ACCIÓN A TOMAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->desempenios()->orderBy('numero','asc')->get() as $dese)
                            {{--*/
                                if(empty(old('estadoDesempenio['.$dese->idDesempenio.']'))){
                                    $selected = (empty($dese->idEstado))?-1:$dese->idEstado;
                                }else{
                                    $selected = old('estadoDesempenio['.$dese->idDesempenio.']');
                                }
                                $readOnly = ($selected == 1)?"readonly":"";
                            /*--}}
                            <tr>
                                <td> {{ $dese->numero }} </td>
                                <td> {{ $dese->nombreDesempenio }} </td>
                                <td>

            <span class="star-cb-group">
             @if($selected==1)
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-3" checked="checked" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="1" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-2" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="2" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-1" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="3" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-1">1</label>
            @elseif($selected==2)
             <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-3"  name="estadoDesempenio[{{$dese->idDesempenio}}]" value="1" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-2" checked="checked" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="2" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-1" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="3" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-1">1</label>

            @elseif($selected==3)
               <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-3"  name="estadoDesempenio[{{$dese->idDesempenio}}]" value="1" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-2"  name="estadoDesempenio[{{$dese->idDesempenio}}]" value="2" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')"id="estadoDesempenio[{{$dese->idDesempenio}}]-1" checked="checked" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="3" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-1">1</label>

            @else
              <input type="radio" id="estadoDesempenio[{{$dese->idDesempenio}}]-3" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="1" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-2"  name="estadoDesempenio[{{$dese->idDesempenio}}]" value="2" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarDese-{{$dese->idDesempenio}}')" id="estadoDesempenio[{{$dese->idDesempenio}}]-1" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="3" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-1" >1</label>
              <input type="radio" checked="checked" style="visibility:hidden;" id="estadoDesempenio[{{$dese->idDesempenio}}]-0" name="estadoDesempenio[{{$dese->idDesempenio}}]" value="-1" /><label for="estadoDesempenio[{{$dese->idDesempenio}}]-0" style="visibility:hidden;">0</label>
            @endif
            </span>

                                  {{--  {!! Form::select('estadoDesempenio['.$dese->idDesempenio.']', array_add($estados, -1, ''),$selected,['class' => 'form-control','onchange' => 'setEnableOrDisableSelect(this,$("#txtAccionTomarDese-'.$dese->idDesempenio.'"))']) !!} --}}
                                </td>
                                <td>
                                    <textarea class="form-control" id="txtAccionTomarDese-{{$dese->idDesempenio}}" name="txtAccionTomarDese[{{$dese->idDesempenio}}]" {{ $readOnly }}>{{$dese->accionTomar}}</textarea>
                                    <input type="hidden" name="desempenios[{{$dese->idDesempenio}}]" value="{{ $dese->idDesempenio }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">PRODUCTOS</th><th style="width: 20%;">(*) VERIFICACIÓN EVIDENCIA</th><th style="width: 20%;">ACCIÓN A TOMAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->productos()->orderBy('numero','asc')->get() as $prod)
                            {{--*/
                                if(empty(old('estadoProducto['.$prod->idProducto.']'))){
                                    $selected = (empty($prod->idEstado))?-1:$prod->idEstado;
                                }else{
                                    $selected = old('estadoProducto['.$prod->idProducto.']');
                                }
                                $readOnly = ($selected == 1)?"readonly":"";
                            /*--}}
                            <tr>
                                <td> {{ $prod->numero }} </td>
                                <td> {{ $prod->nombreProducto }} </td>
                                <td>
                                            <span class="star-cb-group">
                @if($selected==1)
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-3" checked="checked" name="estadoProducto[{{$prod->idProducto}}]" value="1" /><label for="estadoProducto[{{$prod->idProducto}}]-3">3</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-2" name="estadoProducto[{{$prod->idProducto}}]" value="2" /><label for="estadoProducto[{{$prod->idProducto}}]-2">2</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-1" name="estadoProducto[{{$prod->idProducto}}]" value="3" /><label for="estadoProducto[{{$prod->idProducto}}]-1">1</label>
            @elseif($selected==2)
             <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-3"  name="estadoProducto[{{$prod->idProducto}}]" value="1" /><label for="estadoProducto[{{$prod->idProducto}}]">3</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-2" checked="checked" name="estadoProducto[{{$prod->idProducto}}]" value="2" /><label for="estadoProducto[{{$prod->idProducto}}]-2">2</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-1" name="estadoProducto[{{$prod->idProducto}}]" value="3" /><label for="estadoProducto[{{$prod->idProducto}}]-1">1</label>

            @elseif($selected==3)
               <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-3"  name="estadoProducto[{{$prod->idProducto}}]" value="1" /><label for="estadoProducto[{{$prod->idProducto}}]-3">3</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-2"  name="estadoProducto[{{$prod->idProducto}}]" value="2" /><label for="estadoProducto[{{$prod->idProducto}}]-2">2</label>
              <input type="radio" id="estadoProducto[{{$prod->idProducto}}]-1" checked="checked" name="estadoProducto[{{$prod->idProducto}}]" value="3" /><label for="estadoProducto[{{$prod->idProducto}}]-1">1</label>

            @else
              <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-3"  name="estadoProducto[{{$prod->idProducto}}]" value="1" /><label for="estadoProducto[{{$prod->idProducto}}]-3">3</label>
              <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-2"  name="estadoProducto[{{$prod->idProducto}}]" value="2" /><label for="estadoProducto[{{$prod->idProducto}}]-2">2</label>
              <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarProd-{{$prod->idProducto}}')"  id="estadoProducto[{{$prod->idProducto}}]-1" name="estadoProducto[{{$prod->idProducto}}]" value="3" /><label for="estadoProducto[{{$prod->idProducto}}]-1">1</label>
              <input type="radio" checked="checked"  id="estadoProducto[{{$prod->idProducto}}]-0" style="visibility:hidden;" name="estadoProducto[{{$prod->idProducto}}]" value="-1" /><label style="visibility:hidden;" for="estadoProducto[{{$prod->idProducto}}]-0" >0</label>
            @endif
                            </span>


                                  {{--  {!! Form::select('estadoProducto['.$prod->idProducto.']', array_add($estados, -1, ''),$selected,['class' => 'form-control','onchange' => 'setEnableOrDisableSelect(this,$("#txtAccionTomarProd-'.$prod->idProducto.'"))']) !!} --}}
                                </td>
                                <td>
                                    <textarea class="form-control" id="txtAccionTomarProd-{{ $prod->idProducto }}" name="txtAccionTomarProd[{{$prod->idProducto}}]" {{ $readOnly }}>{{$prod->accionTomar}}</textarea>
                                    <input type="hidden" name="productos[{{$prod->idProducto}}]" value="{{ $prod->idProducto }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 40%;">CONOCIMIENTOS</th><th style="width: 15%;">NIVEL</th><th style="width: 20%;">(*) NIVEL COMPETENCIA</th><th style="width: 20%;">ACCIÓN A TOMAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->conocimientos()->orderBy('numero','asc')->get() as $cono)
                            {{--*/
                                if(empty(old('estadoConocimiento['.$cono->idConocimiento.']'))){
                                    $selected = (empty($cono->idEstado))?-1:$cono->idEstado;
                                }else{
                                    $selected = old('estadoConocimiento['.$cono->idConocimiento.']');
                                }
                                $readOnly = ($selected == 1)?"readonly":"";
                            /*--}}
                            <tr>
                                <td> {{ $cono->numero }} </td>
                                <td> {{ $cono->nombreConocimiento }} </td>
                                <td style="background-color:{!! $cono->colorHex !!}"> {{ $cono->nombreNivel }}</td>
                                <td>
                                      <span class="star-cb-group">
                @if($selected==1)
              <input type="radio"  onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-3" checked="checked" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="1" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-3">3</label>
              <input type="radio" id="estadoConocimiento[{{$cono->idConocimiento}}]-2" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="2" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-2">2</label>
              <input type="radio" id="estadoConocimiento[{{$cono->idConocimiento}}]-1" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="3" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-1">1</label>
            @elseif($selected==2)
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-3"  name="estadoConocimiento[{{$cono->idConocimiento}}]" value="1" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-2" checked="checked" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="2" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-1" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="3" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-1">1</label>

            @elseif($selected==3)
                <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-3"  name="estadoConocimiento[{{$cono->idConocimiento}}]" value="1" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-2" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="2" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-1" checked="checked" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="3" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-1">1</label>

            @else
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-3"  name="estadoConocimiento[{{$cono->idConocimiento}}]" value="1" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-2" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="2" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarCono-{{$cono->idConocimiento}}')"  id="estadoConocimiento[{{$cono->idConocimiento}}]-1" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="3" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-1">1</label>
              <input type="radio" checked="checked" style="visibility:hidden;" id="estadoConocimiento[{{$cono->idConocimiento}}]-0" name="estadoConocimiento[{{$cono->idConocimiento}}]" value="-1" /><label for="estadoConocimiento[{{$cono->idConocimiento}}]-0" style="visibility:hidden;">0</label>
            @endif
                            </span>

                               {{--     {!! Form::select('estadoConocimiento['.$cono->idConocimiento.']', array_add($estados, -1, ''),$selected,['class' => 'form-control','onchange' => 'setEnableOrDisableSelect(this,$("#txtAccionTomarCono-'.$cono->idConocimiento.'"))']) !!} --}}
                                </td>
                                <td>
                                    <textarea class="form-control" id="txtAccionTomarCono-{{$cono->idConocimiento}}" name="txtAccionTomarCono[{{$cono->idConocimiento}}]" {{ $readOnly }}>{{$cono->accionTomar}}</textarea>
                                    <input type="hidden" name="conocimientos[{{$cono->idConocimiento}}]" value="{{ $cono->idConocimiento }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">ACTITUDES</th><th style="width: 20%;">(*) NIVEL DE COMPETENCIA</th><th style="width: 20%;">ACCIÓN A TOMAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->actitudes()->orderBy('numero','asc')->get() as $acti)
                            {{--*/
                                if(empty(old('estadoActitud['.$acti->idActitud.']'))){
                                    $selected = (empty($acti->idEstado))?-1:$acti->idEstado;
                                }else{
                                    $selected = old('estadoActitud['.$acti->idActitud.']');
                                }
                                $readOnly = ($selected == 1)?"readonly":"";
                            /*--}}
                            <tr>
                                <td> {{ $acti->numero }} </td>
                                <td> {{ $acti->nombreActitud }} </td>
                                <td>
                                         <span class="star-cb-group">
                @if($selected==1)
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-3" checked="checked" name="estadoActitud[{{$acti->idActitud}}]" value="1" /><label for="estadoActitud[{{$acti->idActitud}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-2" name="estadoActitud[{{$acti->idActitud}}]" value="2" /><label for="estadoActitud[{{$acti->idActitud}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-1" name="estadoActitud[{{$acti->idActitud}}]" value="3" /><label for="estadoActitud[{{$acti->idActitud}}]-1">1</label>
            @elseif($selected==2)
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-3" name="estadoActitud[{{$acti->idActitud}}]" value="1" /><label for="estadoActitud[{{$acti->idActitud}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-2" checked="checked"  name="estadoActitud[{{$acti->idActitud}}]" value="2" /><label for="estadoActitud[{{$acti->idActitud}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-1" name="estadoActitud[{{$acti->idActitud}}]" value="3" /><label for="estadoActitud[{{$acti->idActitud}}]-1">1</label>

            @elseif($selected==3)
                 <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-3" name="estadoActitud[{{$acti->idActitud}}]" value="1" /><label for="estadoActitud[{{$acti->idActitud}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-2"   name="estadoActitud[{{$acti->idActitud}}]" value="2" /><label for="estadoActitud[{{$acti->idActitud}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-1" checked="checked" name="estadoActitud[{{$acti->idActitud}}]" value="3" /><label for="estadoActitud[{{$acti->idActitud}}]-1">1</label>
            @else
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-3" name="estadoActitud[{{$acti->idActitud}}]" value="1" /><label for="estadoActitud[{{$acti->idActitud}}]-3">3</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-2"  name="estadoActitud[{{$acti->idActitud}}]" value="2" /><label for="estadoActitud[{{$acti->idActitud}}]-2">2</label>
              <input type="radio" onclick="setEnableOrDisableSelect(this,'#txtAccionTomarActi-{{$acti->idActitud}}')"  id="estadoActitud[{{$acti->idActitud}}]-1"  name="estadoActitud[{{$acti->idActitud}}]" value="3" /><label for="estadoActitud[{{$acti->idActitud}}]-1">1</label>
              <input type="radio"  style="visibility:hidden;" checked="checked"  id="estadoActitud[{{$acti->idActitud}}]-0" name="estadoActitud[{{$acti->idActitud}}]" value="-1" /><label for="estadoActitud[{{$acti->idActitud}}]-0" style="visibility:hidden;">0</label>
            @endif
                            </span>

                                {{--    {!! Form::select('estadoActitud['.$acti->idActitud.']', array_add($estados, -1, ''),$selected,['class' => 'form-control','onchange' => 'setEnableOrDisableSelect(this,$("#txtAccionTomarActi-'.$acti->idActitud.'"))']) !!} --}}
                                </td>
                                <td>
                                    <textarea class="form-control" id="txtAccionTomarActi-{{$acti->idActitud}}" name="txtAccionTomarActi[{{$acti->idActitud}}]" {{ $readOnly }}>{{$acti->accionTomar}}</textarea>
                                    <input type="hidden" name="actitudes[{{$acti->idActitud}}]" value="{{ $acti->idActitud }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="txtIdResultado" value="{{ Crypt::encrypt($resultado->idResultado) }}" />
            <input type="hidden" name="txtIdTarea" value="{{ Crypt::encrypt($reTar->idTarea) }}" />

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

function setEnableOrDisableSelect(select, textarea){
    if(select.value == "1"){
        $(textarea).val('');
        $(textarea).attr('readOnly','true');
    }else{
        $(textarea).removeAttr("readonly");
    }
}
</script>
@endsection
