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
	<div class="panel-heading">
		<h3 class="panel-title">EXPEDIENTES DE PRODUCTOS</h3>    
	</div>
	<div class="panel-body">
    <input type="hidden" name="idunidad" value="{{$num}}" form="frmprestamo">
    <form role="form" id="search-form">   
      <div class="row">
        <div class="col-md-10"> 
          <label>REGISTRO</label>
          <select id="fregistro" name="fregistro" placeholder="Seleccione uno o varios registros" class="form-control" multiple></select>  
        </div>
        <div class="col-md-2" style="padding-top:20px;">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" class="form-control"/>
          <button type="submit" class="btn btn-success btn-perspective" id="btnConsultar"><i class="fa fa-search"></i> Buscar</button>
        </div>        
      </div>
    </form>  		  
		<div class="table-responsive">		
			<table width="100%" id="dt-exp-productos" class="table table-th-block table-hover table-bordered">
				<thead class="the-box dark full">
					<tr>           
            <th>N° REGISTRO</th>
            <th>NOMBRE COMERCIAL</th>
						<th>UBICACIÓN</th>
            <th>ESTADO</th>           
            <th>-</th>    
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>
</div>

  {{-- modal prestar --}}
    @include('archivo.prestamoEproductos.prestar')
  {{-- /modal prestar --}}

@endsection

@section('js')
{{-- Bootstrap Modal --}}
{!! Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js') !!}

{{-- SElECTIZE JS --}}
{!! Html::script('plugins/selectize-js/dist/js/standalone/selectize.min.js') !!}

<script type="text/javascript">
var table;

$( document ).ready(function() {

  
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
  });
     
  table = $('#dt-exp-productos').DataTable({
      filter:false,
      processing: true,
      serverSide: true,
      lengthChange: false,
      ajax: {processing: true,
          url: "{{ route('dt.exp.prod') }}",
          data: function (d) {
            d.fregistro = $('#fregistro').val();                  
          }
          
      },
      columns: [        	 
          {data:'ID_PRODUCTO', name: 'ID_PRODUCTO'},
          {data: 'NOMBRE_COMERCIAL', name: 'NOMBRE_COMERCIAL',orderable:false},
          {data: 'ubicacion', name: 'estado',orderable:false,searchable:false},
          {data: 'estado', name: 'estado',orderable:false,searchable:false},
          {data: 'prestar', name: 'prestar',orderable:false,searchable:false}
                  
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

  $('#frmprestamo').submit(function(e){
        var formObj = $(this);
        var formURL = formObj.attr("action");
        var formData = new FormData(this);
    $.ajax({
      data: formData,
      url: formURL,
      type: 'post',
      mimeType:"multipart/form-data",
        contentType: false,
          cache: false,
          processData:false,
      beforeSend: function() {
        $('body').modalmanager('loading');
      },
          success:  function (response){
            $('body').modalmanager('loading');
            if(isJson(response)){
              var obj =  JSON.parse(response); 
              alertify.alert("Mensaje de Sistema","<strong><p class='text-justify'>Información registrada exitosamente !</p></strong>",function(){              
                //window.location.reload();  /*solo se recarga la pagina*/
                $("#mdlprestamoproducto").modal('hide');   
                table.ajax.reload();         
              });
              
            }else{
              alertify.alert("Mensaje de Sistema","<strong><p class='text-warning text-justify'>ADVERTENCIA:"+ response +"</p></strong>");
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
        $('body').modalmanager('loading');
        alertify.alert("Mensaje de Sistema","<strong><p class='text-danger text-justify'>ERROR: No se pudo registrar la información!</p></strong>");
              console.log("Error en peticion AJAX!");  
          }
    });
    e.preventDefault(); //Prevent Default action. 

  });
  
  function isJson(str) {
      try {
          JSON.parse(str);
      } catch (e) {
          return false;
      }
      return true;
  }

  var registro = $('#fregistro').selectize({
      valueField: 'ID_PRODUCTO',
      labelField: 'ID_PRODUCTO',        
      searchField: ['ID_PRODUCTO','NOMBRE_COMERCIAL'],
      maxOptions: 10,
      options: [],
      create: false,
      render: {
          option: function(item, escape) {
              return '<div>' +escape(item.ID_PRODUCTO)+' '+escape(item.NOMBRE_COMERCIAL) +'</div>';
            }
      },
      load: function(query, callback) {
              if (!query.length) return callback();
              $.ajax({
                  url: "{{route('find.registros.productos')}}",
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

function fcnprestar(idproducto){

  $.get("{{route('exp.prod.prestar')}}?param="+idproducto, 
      function(data) { 
        if(data.status == 200){
          var obj = data.data;
          console.log(obj);
          $('#idproducto').val(obj.ID_PRODUCTO);
          $("#nombreproducto").val(obj.NOMBRE_COMERCIAL);
          $("#fecha").val(obj.mañana);                 
          if(obj.idestado==5)
          {
            
            $("#fecha").datepicker({              
              startDate: new Date(),
              todayHighlight: true,
              autoclose: true,
            });

            $("#nopres").hide();
            $("#btnprestarEProducto").show();
            $("#sipres").show();         
          }
          else
          {
            $("#btnprestarEProducto").hide();
            $("#sipres").hide();
            if(obj.idestado==4)
            {
              $("#estado").val("AUTORIZADO PARA PRÉSTAMO");
            }
            else
            {
              $("#estado").val(obj.nomestado);
            } //console.log(obj.solicta);
            $("#esolicita").val(obj.solicita['nombresEmpleado']+' '+obj.solicita['apellidosEmpleado']);
            $("#usolicita").val(obj.solicita['nombreUnidad']);          
            $("#nopres").show();
          }
          $("#mdlprestamoproducto").modal('toggle'); 
        }
        else if (data.status == 400){
          $("#frmprestamo").reset();         
          alertify.alert("Ocurrió un error al recuperar los datos");
        }          
  });
  
}

</script>

@endsection
