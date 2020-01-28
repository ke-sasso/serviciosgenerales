
<!-- Modal para busqueda del Establecimiento Origen-->
<div class="modal fade modal-center" id="mdltransferir"  tabindex="-2" role="dialog" >
  <div class="modal-dialog modal-lg" >
    <div class="modal-content">
            <!-- Modal Header -->
      <div class="modal-header bg-success">
          <button type="button" class="close" 
             data-dismiss="modal">
                 <span aria-hidden="true">&times;</span>
                 <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title" id="frmModalLabel">
              TRANSFERIR EXPEDIENTE DE PRODUCTO
          </h4>
      </div>    
      </br>                
        <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{route('exp.prod.transferir')}}" method="POST" class="form form-vertical" role="form" id="frmtransferir" >
            <input type="hidden" name="_token" value="{{ csrf_token() }}">      
          <div class="row"> 
            <div class="form-group col-md-4">
              <div class="input-group">
                <div class="input-group-addon"><b>No. REGISTRO</b></div> 
                <input type="hidden" name="mtidprestamo" id="mtidprestamo">            
                <input type="text" class="form-control" id="mtidproducto" name="mtidproducto" value="" readonly>       
              </div>            
            </div>
            <div class="form-group col-md-8">
              <div class="input-group">
                <div class="input-group-addon"><b>NOMBRE COMERCIAL</b></div>             
                <input type="text" class="form-control" id="mtnomproducto" name="mtnomproducto" value="" readonly>                 
              </div>
            </div>      
          </div>
          <div class="row"> 
            <div class="form-group col-md-12">
              <div class="input-group">
                <div class="input-group-addon"><b>TRANSFERIR A: </b></div>
                <input type="hidden" name="mtidempleado" id="mtidempleado">                         
                <input type="text" class="form-control" id="mtempleado" name="mtempleado" value="" readonly>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary" id="btnBuscarEmpleado"><i class="fa fa-search" ></i></button>
              </span>       
              </div>            
            </div>                  
          </div>        
                                  
          <div class="row" style="margin-left: 5%;"><br><br><br>              
              <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success" id="btnprestarEProducto">GUARDAR</button>
              </div>
          </div>        
          
        </form>  
         
      </div>
        <!-- End Modal Body -->
        <!-- Modal Footer -->
      <div class="modal-footer">                        
          <button type="button" class="btn btn-default"
                  data-dismiss="modal" style="border: 1px solid black;">
                      Cerrar
          </button>                
      </div>
    </div>
  </div>
</div>

<!-- Modal para busqueda del empleado-->
<div class="modal fade modal-center" id="dlgEmpleado"  tabindex="-2" role="dialog" >
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-success">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="frmModalLabel">
                    B&Uacute;SQUEDA DE EMPLEADO
                </h4>
            </div>    
    </br>                
        <!-- Modal Body -->
      <div class="modal-body">
          
              <div class="table">
                <table width="100%" class="table table-hover" id="dt-empleado">
                  <thead class="the-box dark full">
                    <tr>                      
                      <th>NOMBRE</th>                      
                      <th>APELLIDO</th>
                      <th>-</th>
                    </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                </table>
              </div>
         
        </div>
        <!-- End Modal Body -->
        <!-- Modal Footer -->
        <div class="modal-footer">                        
            <button type="button" class="btn btn-default"
                    data-dismiss="modal">
                        Cancelar
            </button>                
        </div>
      </div>
    </div>
</div>
