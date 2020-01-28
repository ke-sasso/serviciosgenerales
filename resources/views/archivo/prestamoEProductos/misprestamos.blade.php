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

  .checkbox2
  {
    width: 25px;
    height: 25px;
    
  } 
  .checkbox1
  {
    width: 20px;
    height: 20px;
    display: inline-block;       
  }

  .vertical{
    width: 70px;
    margin: 2px auto;    
    display: block;
  }
  
</style>
@endsection

@section('contenido')


<div class="panel panel-success">	
	<div class="panel-heading">
		<h3 class="panel-title">SOLICITUDES DE PRÉSTAMOS DE EXPEDIENTES</h3>    
	</div>
	<div class="panel-body">           		  
		<div class="">		
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
            <!--<th>TRANSFERIDA POR</th> -->
            <th>
              <div class=" form-group">
                <div class="input-group ">                       
                  <input type="checkbox" name="checkTodos" id="checkTodos" class="checkbox1">
                  <a onclick="fcnentregado();" class="btn btn-primary btn-xs input-group-addon">Recibir</a>
                </div>
              </div>                         
            </th>         
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
    <br>
    <div class="text-center">    
      <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token"> 
    </div>
	</div>
</div>

 {{-- retornar a archivo --}}
    @include('archivo.prestamoEproductos.retornar')
  {{-- /retornar a archivo --}}

  {{-- transferir a otro empleado de la misma unidad --}}
    @include('archivo.prestamoEproductos.transferir')
  {{-- /transferir a otro empleado de la misma unidad --}}

@endsection

@section('js')
{{-- Bootstrap Modal --}}
{!! Html::script('plugins/bootstrap-modal/js/bootstrap-modalmanager.js') !!}

<script type="text/javascript">
var table;

$( document ).ready(function() {

  $(".datepicker2").datepicker({
    dateFormat: 'YYYY/mm/dd',   
    todayHighlight: true,
    autoclose: true
  });

  table = $('#dt-mis-prestamos').DataTable({
      filter:false,
      info:false,
      processing: true,
      serverSide: true,
      lengthChange: false,
      paginate: false,
      scrollY: "400px",
      scrollX: true,
      ajax: {processing: true,
          url: "{{ route('dt.mis.solicitudes.prestamos') }}",
          data: function (d) {                           
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
          {data: 'empleadoAutoriza', name: 'est.empleadoAutoriza',orderable:false,"width":"20%"}, 
          //{data: 'transfirio', name: 'pre.transfirio',orderable:false}, 
          {data: 'accion', name: 'pre.estadoPrestamo',"width":"5%",orderable:false}             
                  
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
  

    //table.rows().remove();

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

    $('#frmretornar').submit(function(e){
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
                window.location.reload();  /*solo se recarga la pagina*/            
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

  $('#frmtransferir').submit(function(e){
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
                window.location.reload();  /*solo se recarga la pagina*/            
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

  /*seleccionar o no todos*/
  $("#checkTodos").change(function () {   
    $("input:checkbox").prop('checked', $(this).prop("checked"));
  });

  $('#btnBuscarEmpleado').click(function(event) {  
    
      $('#dt-empleado').DataTable({
        processing: true,
        filter:true,
        serverSide: true,
        destroy: true,
        pageLength: 5,
        lengthChange: false,
        ajax: {
          url: "{{route('exp.prod.find.empleado')}}",
         
        },
        columns:[                       
                
                {data: 'nombresEmpleado', name: 'nombresEmpleado'},
                {data: 'apellidosEmpleado', name: 'apellidosEmpleado'},          
               
               {    searchable: false,
                    "mData": null,
                    "bSortable": false,
                    "mRender": function (data,type,full) {      

                        return '<a class="btn btn-primary btn-sm" data-dismiss="modal" onclick="selectEmpleado(\''+data.idEmpleado+'\',\''+data.nombresEmpleado+'\',\''+data.apellidosEmpleado+'\');" >' + '<i class="fa fa fa-check-square-o"></i>' + '</a>';        
                    }
                }                                  
                
            ],
      language: {
        processing: '<div class=\"dlgwait\"></div>',
        "url": "{{ asset('plugins/datatable/lang/es.json') }}"
        
      },                            
      });

      $('#dlgEmpleado').modal('toggle'); 
      
  }); //fin de buscar empleado
  

  
});/*fin documt */

function fcnretornar(idPrestamo)
{
  $.get("{{route('get.prestamo.by.id')}}?param="+idPrestamo, 
        function(data) 
        {                       
          if(data.status == 200)
          {     
            
            console.log(data.idP)            
            $("#mridproducto").val(data.idP);
            $("#mrnomproducto").val(data.noP);
            $("#mridprestamo").val(data.prestamo);
            $("#mrfecha").val('');
            $("#mdlretornar").modal('toggle');

            table.ajax.reload();    /*actualizar solo la tabla*/    
          }
          else if (data.status == 400)
          {                            
            alertify.alert("Mensaje de sistema",data.message);
          }
        }            
  ); 
}

function fcntransferir(idPrestamo)
{
  $.get("{{route('get.prestamo.by.id')}}?param="+idPrestamo, 
        function(data) 
        {                       
          if(data.status == 200)
          {     
            
            console.log(data.idP)            
            $("#mtidprestamo").val(data.prestamo);
            $("#mtidproducto").val(data.idP);
            $("#mtnomproducto").val(data.noP);
            $("#mtidempleado").val('');
            $("#mtempleado").val('');
            $("#mdltransferir").modal('toggle');

            table.ajax.reload();    /*actualizar solo la tabla*/    
          }
          else if (data.status == 400)
          {                            
            alertify.alert("Mensaje de sistema",data.message);
          }
        }            
  );
  
}

function selectEmpleado(idempleado,nomempleado,apeempleado)
{
  $("#mtidempleado").val(idempleado);
  $("#mtempleado").val(nomempleado+' '+apeempleado);
}

function fcnentregado()
{
  var values = new Array();
  $.each($("input[name='listaPrestamos']:checked"), function() {  
    values.push($(this).val());         
  });

  if(values.length==0){
    alertify.alert('Mensaje del Sistema','DEBE SELECCIONAR ALMENOS UNA SOLICITUD!.');
  }
  else
  {
    alertify.confirm('NECESITA CONFIRMACIÓN',"marcar expedientes seleccionados como recibidos?",
      function(){ 
        var tk = $("#token").val();
        $.ajax({
          data:{_token:tk,idPrestamos:values},
          url:   "{{route('confirmar.recibido.exp')}}",
          type:  'post',
         
          beforeSend: function() {
              $('body').modalmanager('loading');
          },
          success:  function (response){
                  $('body').modalmanager('loading');
                  //console.log(response);
                  if(response.status==200){
                    alertify.alert("Mensaje de Sistema","<strong><p class='text-justify'>Información  registrada de forma exitosa!</p></strong>",function(){
                      //var obj =  JSON.parse(response);
                      table.ajax.reload();
                    });
                    
                  }else{                     
                    //console.log(response);
                    alertify.alert("Mensaje de Sistema","<strong><p class='text-warning text-justify'>ADVERTENCIA:"+ response.message +"</p></strong>")
                  }
                },
          error: function(jqXHR, textStatus, errorThrown) {
              $('body').modalmanager('loading');
              alertify.alert("Mensaje de Sistema","<strong><p class='text-danger text-justify'>ERROR: No se pudo registrar la información!</p></strong>");
                    console.log("Error en peticion AJAX!");  
                }
        });
      },
      function(){
        alertify.error('Acción cancelada');    
      }
    );    
  }
  
}

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

  function desistirSolicitud(idPrestamo)
  {
    alertify.confirm('NECESITA CONFIRMACIÓN',"Desistir la solicitud de préstamo?",
    function(){    
      $.get("{{route('desistir.solicitud.prestamo')}}?param="+idPrestamo, 
        function(data) 
        {                       
          if(data.status == 200)
          {                    
            alertify.success("Información registrada exitosamente!");

            table.ajax.reload();    /*actualizar solo la tabla*/    
          }
          else if (data.status == 400)
          {                            
            alertify.alert("Mensaje de sistema",data.message);
          }
        }            
      );
    },
    function(){
      alertify.error('Acción cancelada');    
    }
  );

  }


  
 
</script>

@endsection
