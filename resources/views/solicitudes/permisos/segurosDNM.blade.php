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
            B&uacute;squeda Avanzada de Permisos
            <span class="right-content">
                <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
            </span>
            </a>
        </h3>
    </div>


    
    <div id="collapse-filter" class="collapse" style="height: 0px;">
        <div class="panel-body " >
            {{-- COLLAPSE CONTENT --}}
            <form role="form" id="search-form" >
               <div class="row">
                    <div class="form-group col-sm-5 col-xs-12 col-md-10 col-lg-10">
                        <label>Seleccione la unidad:</label>
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
                    
                    <div class="form-group col-sm-3 col-xs-12">
                        <label>Fecha Inicio:</label>
                       <input type="text" name="fechaInicio"  id="fechaInicio" class="form-control date_masking" placeholder="dd/mm/yyyy" maxlength="10" autocomplete="off">        
                    </div>

                     <div class="form-group col-sm-3 col-xs-12">
                     <label>ESTADO:</label>
                          <select class="form-control" name="procesada" id="procesada" >
                            <option value="" selected>Seleccione...</option>
                                <option value="1">
                                  SOLICITADA</option>
                                <option value="2">
                                  RECEPCIONADA</option>
                                 <option value="3">
                                  INGRESADA</option>
                                 <option value="4">
                                  REINTEGRADA</option>
                            <option value="5">
                                TRAMITE COASEGURADO</option>
                            <option value="6">
                                  TRAMITE CERRADO</option>
                            
                            </select>
                    </div>

                    <div class="form-group col-sm-3 col-xs-12">
                     <label>MOTIVO:</label>
                          <select class="form-control" name="tipo" id="tipo" >
                            <option value="" selected>Seleccione...</option>
                           @foreach($motivos as $mot)
                                <option value="{{$mot->idMotivo}}">
                                  {{$mot->nombre}}
                                </option>
                            @endforeach
                            
                            </select>
                    </div>
               </div>
                    
                <div class="modal-footer" >
                    <div align="center">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" class="form-control"/>
                  <button type="submit"  class="btn btn-success btn-perspective"><i class="fa fa-search"></i> Buscar</button>
                           </div>
                        </div>
                    
                    
            </form>
            {{-- /.COLLAPSE CONTENT --}}
        </div><!-- /.panel-body -->
    </div><!-- /.collapse in -->



    <!-- -------------------------------------------->
    
</div>



    
    <div class="the-box">
	<div class="table-responsive">
	<table class="table table-striped table-hover" id="tr-seguros" style="font-size:14px;" width="100%">
		<thead class="the-box dark full">
			<tr>
				
				<th>CORRELATIVO</th>
                <th>MOTIVO</th>
                <th>FECHA CREACION</th>
                <th>USUARIO</th>
                <th>COD.EMPLEADO</th>
                <th>UNIDAD</th> 
				<th>ESTADO</th>
				<th>-<th>
				
               
               
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
$( document ).ready(function() {
     
     

    var table = $('#tr-seguros').DataTable({
        filter:false,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dt.row.data.seguros.DNM') }}",
            data: function (d) {
                d.unidad= $('#unidad').val();
                d.fechaInicio= $('#fechaInicio').val();
                d.procesada= $('#procesada').val();
                d.tipo= $('#tipo').val();
            }
        },
        columns: [
			     { "data": null }, // <-- This is will your index column
           {data: 'nombre', name: 'mot.nombre'},
            {data: 'fechaCreacion', name: 'se.fechaCreacion'},
            {data: 'idUsuarioCrea', name: 'se.idUsuarioCrea'},
            {data: 'idEmpleadoCrea', name: 'se.idEmpleadoCrea'},
            {data: 'nombreUnidad', name: 'uni.nombreUnidad'},
			      {data: 'nombreEstado', name: 'est.nombreEstado',ordenable:false,searchable:false},
            {data: 'detalle', name: 'detalle',ordenable:false,searchable:false},
            
        ],
        language: {
            "sProcessing": '<div class=\"dlgwait\"></div>',
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
            
            
        },
           "columnDefs": [ {
            "searchable": false,
            "orderable": false,        
            "targets": [0,7],
        } ],

      "order": [[ 2, 'desc' ]]

    } );
	
	
     table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $('#search-form').on('submit', function(e) {

        table.draw();
        e.preventDefault();
        $("#colp").attr("class", "block-collapse collapsed");
        $("#collapse-filter").attr("class", "collapse");
    });

    table.rows().remove();
});

       
</script>
@endsection