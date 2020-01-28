@extends('master')

@section('css')
{!! Html::style('plugins/datatable/css/buttons.dataTables.min.css') !!} 
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
            B&uacute;squeda Avanzada de Conocimientos
            <span class="right-content">
                <span class="right-icon"><i class="fa fa-plus icon-collapse"></i></span>
            </span>
            </a>
        </h3>
    </div>
    @include('edc.admin.capacitaciones.analisis.auxs.filter-v2')
</div>

<form id="form" method="post" action="{{ route('rh.capacitaciones.items.guardar') }}">
<!-- BEGIN DATA TABLE -->
<div class="the-box">
    <div class="table-responsive">
    <table class="table table-striped table-hover" id="dt-capacitaciones-items" style="font-size:13px;" width="100%">
        <thead class="the-box dark full">
            <tr>
                <th width="5%"></th>
                <th>Id Resultado</th>
                <th>Id Empleado</th>
                <th width="30%">Nombre Empleado</th>
                <th>Plaza</th>
                <th>Id Unidad</th>
                <th>Unidad</th>
                
                <th>Id Tipo Conocimiento</th>
                <th width="50%">Conocimiento</th>
                <th>Evaluación</th>
                <th>Evaluación Periodo</th>
                <th width="5%"><input type="checkbox" name="selectAll" id="selectAll"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    </div><!-- /.table-responsive -->
</div><!-- /.the-box .default -->
<!-- END DATA TABLE -->
@include('edc.admin.capacitaciones.analisis.auxs.add-capa-form')
</form>
@endsection

@section('js')
{!! Html::script('plugins/datatable/js/dataTables.buttons.min.js') !!}
{!! Html::script('plugins/datatable/js/buttons.flash.min.js') !!}
{!! Html::script('plugins/datatable/js/jszip.min.js') !!}
{!! Html::script('plugins/datatable/js/pdfmake.min.js') !!}
{!! Html::script('plugins/datatable/js/vfs_fonts.js') !!}
{!! Html::script('plugins/datatable/js/buttons.html5.min.js') !!}
{!! Html::script('plugins/datatable/js/buttons.print.min.js') !!}

<script>
$(document).ready(function() {
  var searchCount = 0;

  var table = $('#dt-capacitaciones-items').DataTable({
      serverSide: true,
      processing: true,
      searching: false,
      ajax: {
        url: "{{ route('dt.row.data.rh.capacitaciones.conocimientos') }}",
        data: function(d){
          d.searchCount = searchCount;  
          d.unidad = $('#unidad').val();
          d.plazaFuncional = $('#plazaFuncional').val();
          d.estado = $('#estado').val();
          d.evaluacion =  $('#evaluacion').val();
          d.categoria =  $('#categoria').val();
        },
      },
      columns: [
          {
              "className":      'details-control',
              "orderable":      false,
              "data":           null,
              "defaultContent": ''
          },
          {data: 'idResultado', name: 'idResultado'},//1
          {data: 'idEmpleado', name: 'idEmpleado'},//2
          {data: 'nombreEmpleado', name: 'nombreEmpleado'},//3
          {data: 'nombrePlaza', name: 'nombrePlaza'},//4
          {data: 'idUnidad', name: 'idUnidad'},//5
          {data: 'nombreUnidad', name: 'nombreUnidad'},//6
          
          {data: 'idTipoConocimiento', name: 'idTipoConocimiento'},//7
          {data: 'nombreTipoConocimiento', name: 'nombreTipoConocimiento'},//8
          {data: 'nombreEvaluacion', name: 'nombreEvaluacion'},//9
          {data: 'periodoEvaluacion', name: 'periodoEvaluacion'},//10
          {data: 'add', name: 'add', orderable: false, searchable: false}  
      ],
      columnDefs: [
          {
              "targets": [1,2,4,5,6,7,9,10],
              "visible": false
          }
      ],
      language: {
          "url": "{{ asset('plugins/datatable/lang/es.json') }}"
      },
      order: [[7, 'asc'],[5, 'asc'],[2, 'asc']],
      scrollY: "400px",
      scrollCollapse: true,
      paging: false,
      dom: 'Bfrtip',
      buttons: [
        {
          text:'Exportar a Excel',
          action: function (e,dt,node,config) {
            var params = {
              "searchCount" : searchCount,
              "unidad" : $('#unidad').val(),
              "plazaFuncional" : $('#plazaFuncional').val(),
              "estado" : $('#estado').val(),
              "evaluacion" :  $('#evaluacion').val(),
              "categoria" :  $('#categoria').val()
            };
            if(table.data().any()){
              $.ajax({
                  cache: false,
                  data:   params,
                  url:   "{{ route('rh.capacitaciones.conocimientos.expExcel') }}",
                  type:  'get',
                  beforeSend: function() {
                      //$('body').modalmanager('loading');
                  },
                  success:  function (r){
                    var a = document.createElement("a");
                    a.href = r.file; 
                    a.download = r.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                  },
                  error: function(data){
                      // Error...
                      var errors = $.parseJSON(data.responseText);
                      console.log(errors);
                      $.each(errors, function(index, value) {
                          $.gritter.add({
                              title: 'Error',
                              text: value
                          });
                      });
                  }
              });
            }
           
          }
        }
      ],
      "drawCallback": function ( settings ) {
        var api = this.api();
        var rows = api.rows( {page:'current'} ).nodes();
        var last=null;

        api.column(8, {page:'current'} ).data().each( function ( group, i ) {
          if ( last !== group ) {
            $(rows).eq( i ).before(
              '<tr class="group"><td colspan="5">'+group+'</td></tr>'
            );
            last = group;
          }
        });
      }
  });


  $('#dt-capacitaciones-items tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row(tr);

      if ( row.child.isShown() ) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');
      }
      else {
          // Open this row
          row.child( format(row.data()) ).show();
          tr.addClass('shown');
      }
  });

  $('#search-form').on('submit', function(e) {
    searchCount++;
    table.draw();
    e.preventDefault();
    $("#colp").attr("class", "block-collapse collapsed");
    $("#collapse-filter").attr("class", "collapse");
  });
});

function format (d) {
  return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" width="100%">'+
    '<tr>'+
        '<td><b>Evaluación:</b>&nbsp;&nbsp;</td>'+
        '<td>'+d.nombreEvaluacion+' ('+d.periodoEvaluacion+')</td>'+
    '</tr>'+ 
    '<tr>'+
        '<td><b>Unidad:</b>&nbsp;&nbsp;</td>'+
        '<td>'+d.nombreUnidad+'</td>'+
    '</tr>'+       
    '<tr>'+
        '<td><b>Plaza Funcional:</b>&nbsp;&nbsp;</td>'+
        '<td>'+d.nombrePlaza+'</td>'+
    '</tr>'+ 
  '</table>';
}
</script>
@include('edc.admin.capacitaciones.analisis.auxs.add-capa-js')
@endsection
