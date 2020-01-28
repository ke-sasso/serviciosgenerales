@extends('master')

@section('css')

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
            B&uacute;squeda de solicitudes préstamos
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
		<h3 class="panel-title">SOLICITUDES DE PRÉSTAMOS</h3>    
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
            <th>AUTORIZAR</th>
            <th>DENEGAR</th>    
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

<script type="text/javascript">
var  table;
$( document ).ready(function() {

  $(".datepicker2").datepicker({    
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom auto",
  });

  table = $('#dt-solicitudes').DataTable({
      filter:false,
      processing: true,
      serverSide: true,
      lengthChange: false,
      ajax: {processing: true,
          url: "{{ route('dt.solicitudes.to.autorizar') }}",
          data: function (d) {
            d.fidproducto = $('#fidproducto').val();
            d.fnomproducto = $('#fnomproducto').val();
            d.ffecha = $('#ffecha').val();
            d.fusolicita = $("#fusolicita").val();                 
          }
          
      },
      columns: [           
        {data: 'noRegistroExpediente', name: 'noRegistroExpediente'},
        {data: 'nombreExpediente', name: 'nombreExpediente',orderable:false},
        {data: 'eSolicita', name: 'eSolicita',orderable:false},
        {data: 'nombreUnidad', name: 'nombreUnidad',orderable:false},
        {data:'fechaPrestamo', name:'fechaPrestamo'},
        {data:'autorizar', name:'autorizar',orderable:false,className:'text-center'},
        {data:'denegar', name:'denegar',orderable:false,className:'text-center'}                       
      ],
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
  
  
});/*fin documt */

function autorizar(idP,opcion)
{ var msj=""; 
  if(opcion==4)
  {
    msj ="Aprobar solicitud de préstamo de expediente?";
  }
  else if(opcion==6)
  {
    msj ="<p class='text-warning'>Denegar solicitud de préstamo de expediente?</p>";
  }
  alertify.confirm('NECESITA CONFIRMACIÓN',msj,
    function(){    
      $.get("{{route('autorizar.desde.sistema')}}?idprestamo="+idP+"&opcion="+opcion, 
        function(data) 
        {                       
          if(data.status == 200)
          {                    
            alertify.success("Información registrada exitosamente!");

            table.ajax.reload();   /*actualizar página*/    
          }
          else if (data.status == 400)
          {                            
            alertify.alert("Mensaje de sistema","<strong><p class='text-danger text-justify'>ERROR: No se pudo registrar la información!</p></strong>");
          }
        }            
      );
    },
    function(){
      //alertify.error('Acción cancelada');    
    }
  );

}

</script>

@endsection
