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
    <h4 class="small-title">MOSTRAR TAREA</h4>

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
            <label>Tarea {{ $reTar->numero }}</label>
            <textarea class="form-control" readonly="true" rows="4">{{ $reTar->nombreTarea }}</textarea>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>   
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">CRITERIO DE DESEMPEÑO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->desempenios()->orderBy('numero','asc')->get() as $dese)
                            <tr>
                                <td> {{ $dese->numero }} </td>
                                <td> {{ $dese->nombreDesempenio }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>   
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">PRODUCTOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->productos()->orderBy('numero','asc')->get() as $prod)
                            <tr>
                                <td> {{ $prod->numero }} </td>
                                <td> {{ $prod->nombreProducto }} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>   
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 40%;">CONOCIMIENTOS</th><th style="width: 15%;">NIVEL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->conocimientosNivel()->orderBy('numero','asc')->get() as $cono)
                            <tr>
                                <td> {{ $cono->numero }} </td>
                                <td> {{ $cono->nombreConocimiento }} </td>
                                <td style="background-color:{!! $cono->colorHex !!}"> {{ $cono->nombreNivel }}</td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="table-responsive-2">
                <table class="table table-th-block" style="font-size:13px;" width="100%">
                    <thead>   
                        <tr style="background-color:#C9DFE8">
                            <th style="width: 5%;"></th><th style="width: 55%;">ACTITUDES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reTar->actitudes()->orderBy('numero','asc')->get() as $acti)
                            <tr>
                                <td> {{ $acti->numero }} </td>
                                <td> {{ $acti->nombreActitud }} </td>
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
            <a href="{{ URL::previous() }}" class="btn btn-warning btn-perspective"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    
</script>
@endsection
