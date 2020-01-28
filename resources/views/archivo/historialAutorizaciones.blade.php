@extends('master')

@section('css')
{{-- SElECTIZE JS --}}
{!! Html::style('plugins/selectize-js/dist/css/selectize.bootstrap3.css') !!}

<style type="text/css">

  body {
      
    }
  .dlgwait {
      display:    inline;
      position:   fixed;
      z-index:    1000;
      top:        0;
      left:       0;
      height:     100%;
      width:      100%;
      background: rgba( 255, 255, 255, .3 ) 
                  url("{{ asset('/img/ajax-loader.gif') }}") 
                  50% 50% 
                  no-repeat;
  }
    
  /* When the body has the loading class, we turn
     the scrollbar off with overflow:hidden */
  body.loading {
      overflow: hidden;
  }

  /* Anytime the body has the loading class, our
     modal element will be visible */
  body.loading .dlgwait {
      display: block;
  }
   
  #dt-transferencias{
    font-size: 12px;
  }

</style>
@endsection

@section('contenido')
<div class="panel panel-success">
    <div class="panel-heading" >
        <h3 class="panel-title">
            <a class="block-collapse collapsed" id='colp' data-toggle="collapse" href="#collapse-filter">
            B&uacute;squeda de autorizaciones
            <span class="right-content">
                <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
            </span>
            </a>
        </h3>
    </div>   
    <div id="collapse-filter" class="collapse">
        <div class="panel-body">

            {{-- COLLAPSE CONTENT --}}
            <form role="form" id="search-form">              
              <div class="row">
                <div class="col-sm-12 col-md-4 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>No. REGISTRO</b></div>
                    <input type="text" class="form-control" id="fidproducto" name="fidproducto" value="">                  
                  </div>
                </div>
                <div class="col-sm-12 col-md-8 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>NOMBRE DE REGISTRO</b></div>
                    <input type="text" class="form-control" id="fnomproducto" name="fnomproducto" value="">                      
                  </div>
                </div>
                <div class="col-sm-12 col-md-12 form-group">                  
                    <label>EMPLEADO SOLICITANTE</label>
                    <select id="fesolicita" name="fesolicita" placeholder="Buscar epleado solicitante" class="form-control"></select>              
                </div>
                <div class="col-sm-12 col-md-12 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>UNIDAD SOLICITANTE</b></div>
                      <select class="form-control" id="fusolicita" name="fusolicita">
                        <option value="" selected>Seleccione una unidad</option>
                        @foreach($unidades as $u)
                        <option value="{{$u->idUnidad}}">{{$u->nombreUnidad}}</option>
                        @endforeach
                      </select>                      
                  </div>
                </div>
                <div class="col-sm-12 col-md-6 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>FECHA DE SOLICITUD DE PRÉSTAMO</b></div>
                    <input type="text" class="form-control datepicker2" id="ffecha" name="ffecha" value="" data-date-format="dd-mm-yyyy" autocomplete="off">                      
                  </div>
                </div>                
              </div>
                               
              <div class="modal-footer" >
                <div align="center">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}" class="form-control"/>
                <button type="submit" class="btn btn-success btn-perspective" id="btnConsultar"><i class="fa fa-search"></i> Buscar</button>
               </div>
              </div>
                  
                    
            </form>
            {{-- /.COLLAPSE CONTENT --}}
        </div><!-- /.panel-body -->
    </div><!-- /.collapse in -->
</div>

<div class="panel panel-success">	
	<div class="panel-heading">
		<h3 class="panel-title">HISTORIAL DE AUTORIZACIONES</h3>    
	</div>
	<div class="panel-body">    		  
		<div class="table-responsive">		
			<table width="100%" id="dt-solicitudes" class="table table-th-block table-hover table-bordered">
				<thead class="the-box dark full">
					<tr>           
            <th>N° REGISTRO</th>
            <th>NOMBRE DE REGISTRO</th>
						<th>EMPLEADO SOLICITA</th>
            <th>UNIDAD SOLICITA</th>           
            <th>FECHA DE SOLICITUD</th>
            <th>FECHA DE AUTORIZACI&Oacute;N</th>
            <th>ESTADO</th>    
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection

@section('js')
{{-- Bootstrap Modal --}}
{!! Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js') !!}

{{-- SElECTIZE JS --}}
{!! Html::script('plugins/selectize-js/dist/js/standalone/selectize.min.js') !!}

<script type="text/javascript">
var  table;
$( document ).ready(function() {

  $(".datepicker2").datepicker({    
    todayHighlight: true,
    autoclose: true
  });

  table = $('#dt-solicitudes').DataTable({
      filter:false,
      processing: true,
      serverSide: true,
      lengthChange: false,
      ajax: {processing: true,
          url: "{{ route('dt.historial.autorizaciones') }}",
          data: function (d) {
            d.fidproducto = $('#fidproducto').val();
            d.fnomproducto = $('#fnomproducto').val();
            d.fesolicita = $('#fesolicita').val();
            d.fusolicita = $('#fusolicita').val();
            d.ffecha = $('#ffecha').val();                 
          }
          
      },
      columns: [           
        {data: 'noRegistroExpediente', name: 'noRegistroExpediente'},
        {data: 'nombreExpediente', name: 'nombreExpediente',orderable:false},
        {data: 'eSolicita', name: 'eSolicita',orderable:false},
        {data: 'nombreUnidad', name: 'nombreUnidad',orderable:false},
        {data:'fechaPrestamo', name:'fechaPrestamo'},
        {data:'fechaAutorizacion', name:'fechaAutorizacion'},
        {data:'estado', name:'estado'}                               
      ],
      order: [[4, 'desc']],
      language: {
          "sProcessing": '<div class=\"dlgwait\"></div>',
          "url": "{{ asset('plugins/datatable/lang/es.json') }}"         
      },
       columnDefs: [
          {                
            "visible": false             
          }
      ]    
  }); //end Datatable

  $('#search-form').on('submit', function(e) { 

        table.draw();
        e.preventDefault();
        $("#colp").attr("class", "block-collapse collapsed");
        $("#collapse-filter").attr("class", "collapse");
    });

    table.rows().remove();


  /*llenado del combobox de empleados*/
  var esolicita = $('#fesolicita').selectize({
        valueField: 'idEmpleado',
        labelField: 'nombreempleado',        
        searchField: ['nombresEmpleado','apellidosEmpleado'],
        maxOptions: 10,
        options: [],
        create: false,
        render: {
            option: function(item, escape) {
                return '<div>' +escape(item.nombresEmpleado)+' '+escape(item.apellidosEmpleado) +'</div>';
              }
        },
        load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: "{{route('find.empleados.to.selectize')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: query
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res.data);
                    }
                });
        }
  }); /*fin del selectize*/
  
});/*fin documt */


</script>

@endsection
