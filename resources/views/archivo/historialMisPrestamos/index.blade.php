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
 
  #dt-mis-prestamos
  {
    font-size: 12px;
  }

  td.details-control 
  {   
    background: url("{{ asset('/plugins/datatable/images/details_open.png') }}") no-repeat  center;
    cursor: pointer;  
  }
  tr.shown td.details-control 
  {        
    background: url("{{ asset('/plugins/datatable/images/details_close.png') }}") no-repeat  center;
  }

</style>
@endsection

@section('contenido')
<div class="panel panel-success">
    <div class="panel-heading" >
        <h3 class="panel-title">
            <a class="block-collapse collapsed" id='colp' data-toggle="collapse" href="#collapse-filter">
            B&uacute;squeda avanzada de préstamos
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
                    <input type="text" class="form-control" id="idproducto" name="idproducto" value="">                      
                  </div>
                </div>
                <div class="col-sm-12 col-md-8 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>NOMBRE DE REGISTRO</b></div>
                    <input type="text" class="form-control" id="nomproducto" name="nomproducto" value="">                      
                  </div>
                </div>
                <div class="col-sm-12 col-md-4 form-group">
                  <div class="input-group ">
                    <div class="input-group-addon"><b>FECHA PRÉSTAMO</b></div>
                    <input type="text" class="form-control datepicker2" id="fecha" name="fecha" value="" data-date-format="dd-mm-yyyy">                      
                  </div>
                </div>
                <div class="form-group col-sm-12 col-md-4">
                    <div class="input-group ">
                      <div class="input-group-addon"><b>Estado</b></div>
                      <select class="form-control" name="estado" id="estado">
                        <option value="" selected>Seleccione...</option>
                      @if(!empty($estados))
                        @foreach($estados as $e)
                        <option value="{{$e->idEstadoPrestamo}}">{{$e->nombreEstado}}</option>
                        @endforeach
                      @endif                   
                      </select> 
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
    <h3 class="panel-title">PRÉSTAMOS DE EXPEDIENTES</h3>    
  </div>
  <div class="panel-body">                
    <div class="table-responsive">    
      <table width="100%" id="dt-mis-prestamos" class="table table-th-block table-hover table-bordered">
        <thead class="the-box dark full">
          <tr> 
            <th>-</th>          
            <th>N° REGISTRO</th>
            <th>NOMBRE COMERCIAL</th>
            <th>FECHA DE PRÉSTAMO</th>            
            <th>ESTADO</th>
            <th>UNIDAD AUTORIZA</th>
            <th>AUTORIZADA POR</th>          
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
var table;

$( document ).ready(function() {

  $(".datepicker2").datepicker({     
    todayHighlight: true,
    autoclose: true,
    orientation: "bottom auto",
  });

  table = $('#dt-mis-prestamos').DataTable({
      filter:false,
      processing: true,
      serverSide: true,
      lengthChange: false,
      ajax: {processing: true,
          url: "{{ route('exp.prod.dt.misprestamos') }}",
          data: function (d) {
            d.idproducto = $('#idproducto').val();
            d.nomproducto = $('#nomproducto').val();
            d.fecha = $('#fecha').val();
            d.estado = $('#estado').val();                  
          }
          
      },
      columns: [ 
          {
            "className":      'details-control',
            "orderable":      false,
            "searchable":     false,
            "data":           '',
            "width":"5%",
            "name": '',
            "defaultContent": ''
          },         
          {data:'noRegistroExpediente', name: 'pre.noRegistroExpediente',orderable:false},
          {data: 'nombreExpediente', name: 'pre.nombreExpediente',orderable:false,"width":"20%"},   
          {data: 'fechaPrestamo', name: 'pre.fechaPrestamo'},
          {data: 'nombreEstado', name: 'est.nombreEstado',orderable:false},
          {data: 'uautoriza', name: 'est.uautoriza',orderable:false,"width":"20%"},
          {data: 'empleadoAutoriza', name: 'est.empleadoAutoriza',orderable:false,"width":"20%"}
                  
      ],
      order: [[3, 'desc']],
      language: {
          "sProcessing": '<div class=\"dlgwait\"></div>',
          "url": "{{ asset('plugins/datatable/lang/es.json') }}"
          
          
      },
       columnDefs: [
          {                
            "visible": false,                         
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

     // Add event listener for opening and closing details
    $('#dt-mis-prestamos tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );

      if ( row.child.isShown() ) 
      {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');            
      }
      else 
      {
          // Open this row
          row.child( template(row.data()) ).show();
          tr.addClass('shown');           
      }
    });
  
  function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
  }

  

  
});/*fin documt */




  function template(d)
  {    
    if(d.transfirio){ et = d.transfirio;}else{et= "N/A";}
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" width="100%" class="detalle">'+
    '<tr>'+
        '<td width="20%"><b>TRANSFERIDA POR:</b>&nbsp;&nbsp;</td>'+
        '<td width="80%">'+et+'</td>'+        
    '</tr>'+     
  '</table>';
  }



</script>

@endsection
