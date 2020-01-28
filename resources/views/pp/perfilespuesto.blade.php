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
		<strong>Auchh!</strong>
		Algo ha salido mal.	{{ Session::get('msnError') }}
	</div>
@endif
<div class="panel panel-success">
    <div class="panel-heading" >
        <h3 class="panel-title">
            <a class="block-collapse collapsed" id='colp' data-toggle="collapse" href="#collapse-filter">
            B&uacute;squeda Avanzada de Perfiles de Puesto
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
                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                        <label>Empleado:</label>
                        <input type="text" name="empleado" id="empleado" value="" class="form-control">
                    </div>

                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                        <label>Unidad:</label>
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
                    
                     <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                     <label>Plaza Funcional:</label>
                          <select class="form-control" name="pfun" id="pfun" >
                            <option value="" selected>Seleccione...</option>
                                @foreach($plazasfun as $pfun)
                                    <option value="{{$pfun->idPlazaFuncional}}">
                                      {{$pfun->nombrePlaza}}
                                    </option>
                                @endforeach
                            </select>
                    </div>

                    <div class="form-group col-sm-12 col-xs-12 col-md-6 col-lg-6">
                     <label>Plaza Nominal:</label>
                          <select class="form-control" name="pnom" id="pnom" >
                            <option value="" selected>Seleccione...</option>
                                @foreach($plazasnom as $pnom)
                                    <option value="{{$pnom->idPlazaNominal}}">
                                      {{$pnom->nombrePlazaNominal}}
                                    </option>
                                @endforeach
                            </select>
                    </div>
               </div>
                    
                <div class="modal-footer" >
                    <div align="center">
                             <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" class="form-control"/>
                  <button type="submit" class="btn btn-success btn-perspective"><i class="fa fa-search"></i> Buscar</button>
                           </div>
                        </div>
                    
                    
            </form>
            {{-- /.COLLAPSE CONTENT --}}
        </div><!-- /.panel-body -->
    </div><!-- /.collapse in -->
</div>

<div class="the-box">
    
    <!-- BEGIN DATA TABLE -->
	<div class="table-responsive">
	<table class="table table-striped table-hover" id="dt-perfilesp" style="font-size:13px;" width="100%">
		<thead class="the-box dark full">
			<tr>
                
                <th>Cod. Empleado</th>
                <th>Empleado</th>
				<th>Plaza Funcional</th>
                <th>Plaza Nominal</th>
				<th>Unidad</th>
                <th>-</th>
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
$(document).ready(function(){
	var table = $('#dt-perfilesp').DataTable({
        serverSide: true,
        ajax: {
            url: "{{ route('dt.empleados.perfilesp') }}",
             data: function (d) {
                d.unidad= $('#unidad').val();
                d.empleado= $('#empleado').val();
                d.pfun= $('#pfun').val();
                d.pnom= $('#pnom').val();
            }
        },
        columns: [
        	
            {data: 'idEmpleado', name: 'idEmpleado'},
            {data: 'empleado', name: 'empleado'},
            {data: 'nombrePlaza', name: 'nombrePlaza'},
            {data: 'nombrePlazaNominal', name: 'nombrePlazaNominal'},
            {data: 'nombreUnidad', name: 'nombreUnidad'},
            {data: 'mostrar', name: 'mostrar', orderable: false, searchable: false}
        ],
        columnDefs: [
            {
                
            }
        ],
        language: {
            "url": "{{ asset('plugins/datatable/lang/es.json') }}"
        },
        order: [[1, 'asc'],[4, 'asc']]
    });

    $('#search-form').on('submit', function(e) {

        table.draw();
        e.preventDefault();
        $("#colp").attr("class", "block-collapse collapsed");
        $("#collapse-filter").attr("class", "collapse");
    });

    table.rows().remove();
    table.ajax.reload();
	
});


</script>
@endsection
