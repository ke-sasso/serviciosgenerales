@extends('master')

@section('css')

@endsection

@section('contenido')
{{-- MENSAJE ERROR VALIDACIONES --}}
@if($errors->any())
    <div class="alert alert-warning square fade in alert-dismissable">
        <button class="close" aria-hidden="true" data-dismiss="alert" type="button">x</button>
        <strong>Oops!</strong>
        Debes corregir los siguientes errores para poder continuar      
        <ul class="inline-popups">
            @foreach ($errors->all() as $error)
                <li  class="alert-link">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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
        Algo ha salido mal. {{ Session::get('msnError') }}
    </div>
@endif
<div class="the-box">
    <h4 class="small-title">NUEVA ASIGNACIÓN INSPECCIONES</h4>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label class='text-warning'>Los campos con asterico (*) son requeridos</label>
            </div>
        </div><!--./col-sm-12-->
    </div>
    <form id="form" method="post" action="{{ route('asignacion.est.actualizar') }}">
    
    <div class="row">
        <div class="form-group col-sm-6 col-xs-12">
            <label>Origen solicitud</label>
            <input type="text" class="form-control" disabled value="Unidades DNM">
            <p class="help-block">Tipo de origen de nueva solitud insepcción</p>
        </div>
        <div class="form-group col-sm-6 col-xs-12">
            <label>Unidad</label>
            <input type="text" class="form-control" disabled value="Unidad Juridica (UJ)">
            <p class="help-block">Unidad de la que proviene inicialmente la solicitud</p>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a class="block-collapse collapsed" data-toggle="collapse" href="#collapse-listado-solicitantes">
                Datos solicitud establecimiento
                <span class="right-content">
                    <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
                </span>
                </a>
            </h3>
        </div>
        <div id="collapse-listado-solicitantes" class="collapse" style="height: 0px;">
            <div class="panel-body">
                {{-- COLLAPSE CONTENT --}}
                @if($est->idTramite == 1 || $est->idTramite == 2){{-- SE MUESTRA ESTA VISTA SOLO SI ES APERTURA --}}
                    @include('solicitudes.establecimientos.datasoli')
                @else
                    @include('solicitudes.establecimientos.datacat')
                @endif
                {{-- /.COLLAPSE CONTENT --}}
            </div><!-- /.panel-body -->
        </div><!-- /.collapse in -->
    </div>
    <div class="row">
        <div class="form-group col-sm-6 col-xs-12">
            <label>Fecha recepción</label>
            <input type="text" name="txtFechaRecepcion" id="txtFechaRecepcion" class="form-control date_masking_g" value="{{ (empty($soli->fecha_recepcion)||$soli->fecha_recepcion=='0000-00-00')?'':date_format(date_create($soli->fecha_recepcion),'d-m-Y') }}" placeholder="dd-mm-yyyy">
            <p class="help-block">Fecha de recepción inspecciones</p>
        </div>
        <div class="form-group col-sm-6 col-xs-12">
            <label>* Fecha probable inspección</label>
            <input type="text" name="txtFechaProbable" id="txtFechaProbable" class="form-control date_masking_g" value="{{ (empty($soli->fecha_probable_inspeccion)||$soli->fecha_probable_inspeccion=='0000-00-00 00:00:00')?'':date_format(date_create($soli->fecha_probable_inspeccion),'d-m-Y') }}" placeholder="dd-mm-yyyy">
            <p class="help-block">Fecha tentativa para realiar la inspección</p>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-6 col-xs-12">
            <label>* Tipo solicitud</label>
            @if($soli->id_estado == 7)
                {!! Form::select('cmbTipo_view',$tipos,$soli->id_tipo, ['class'=>'form-control', 'id' => 'cmbTipo_view','onchange' => 'fillCmbTipoProcedimiento();','disabled'=>'true'] ) !!}
                <input type="hidden" name="cmbTipo" value="{{$soli->id_tipo}}"/>
            @else
                {!! Form::select('cmbTipo',$tipos,$soli->id_tipo, ['class'=>'form-control', 'id' => 'cmbTipo','onchange' => 'fillCmbTipoProcedimiento();'] ) !!}
            @endif
            <p class="help-block">Tipo de solicitud dentro de inspecciónes</p>

        </div>
        <div class="form-group col-sm-6 col-xs-12">
            <label>* Tipo procedimiento</label>
            @if($soli->id_estado == 7)
                {!! Form::select('cmbTipoProcedimiento_view',$procedimientos,$soli->id_procedimiento, ['class'=>'form-control', 'id' => 'cmbTipoProcedimiento_view','disabled'=>'true'] ) !!}
                <input type="hidden" name="cmbTipoProcedimiento" value="{{$soli->id_procedimiento}}"/>
            @else
                {!! Form::select('cmbTipoProcedimiento',$procedimientos,$soli->id_procedimiento, ['class'=>'form-control', 'id' => 'cmbTipoProcedimiento'] ) !!}
            @endif
            <p class="help-block">Procedimiento que se aplicará a la solicitud en inspecciones</p>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12 col-xs-12">
            <label>Dirección solicitada</label>
            <textarea class="form-control" name ="txtDireccionSolicitada" id="txtDireccionSolicitada" placeholder="Es obligatorio llenar para el procedimiento TRASLADO DE ESTABLECIMIENTOS">{{ $soli->direccion_solicitud }}</textarea>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12 col-xs-12">
            <label>* Inspectores</label>
                {!! Form::select('cmbInspectores[]',$inspectores,$soli->asignaciones()->lists('id_usuario'), ['class'=>'form-control chosen-select','multiple' => 'multiple','data-placeholder' => 'Inspecctores encargados de llevar a cabo la inspección' ]) !!}
            </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-12 col-xs-12">
            <label>Observaciones/Comentarios</label>
            <textarea class="form-control" name ="txtObservacion" id="txtObservacion" placeholder="Es obligatorio llenar para los casos donde solo se seleccione un inspecctor responsable">{{ $soli->observacion }}</textarea>
        </div>
    </div>
    @if($soli->id_estado == 7)
        <div class="row">
            <div class="form-group col-sm-6 col-xs-12">
                <label>* Procede reinspección</label>
                {!! Form::select('cmbReinspeccion',[1=>'Si',2=>'No'],1, ['class'=>'form-control', 'id' => 'cmbReinspeccion'] ) !!}
                <p class="help-block">Si no procede reinspección se cierra totalmente el proceso</p>
            </div>
        </div>
    @endif
    <hr>
    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="txtIdSoliIns" value="{{ $idSoliIns }}"/>
            <a href="{{ route('asignacion.est.solicitudes') }}" class="btn btn-warning btn-perspective"><i class="fa fa-ban"></i> Cancelar</a>
            <button type="submit" class="btn btn-primary btn-perspective"><i class="fa fa-floppy-o"></i> Guardar</button>
        </div>
        <div class="col-md-4">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <br>
                <p class='text-warning'><b>NOTA:</b> Si el estado de la solicitud es REASIGNAR,REPROGRAMAR, INSPECCIÓN/NO CUMPLE y se guarda la edición esto pondrá la solicitud en estado ESPERA DE CONFIRMACIÓN</p>
            </div>
        </div><!--./col-sm-12-->
    </div>
    </form>
</div>
@endsection
@section('js')
<script type="text/javascript">
$(function(){
     $("#collapse-listado-solicitantes").collapse('show');
});
function fillCmbTipoProcedimiento() {
    $.ajax({
        data:  {tipo:$('#cmbTipo').val(),_token: '{{ csrf_token() }}'},
        url:   "{{ route('aux.soli.ajax.procedimientos')}}",
        type:  'post',
        success:  function (response){
            $('#cmbTipoProcedimiento').html(response);
            $("#cmbTipoProcedimiento").trigger("chosen:updated");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Ha sucedido un problema al cargar los procedimientos!");
        }
    });
}
</script>
@endsection
