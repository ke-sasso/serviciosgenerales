
<!-- Modal para busqueda del Establecimiento Origen-->
<div class="modal fade modal-center" id="mdlretornar"  tabindex="-2" role="dialog" >
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
                    RETORNAR EXPEDIENTE A ARCHIVO
                </h4>
            </div>    
    </br>                
        <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{route('exp.prod.retornar')}}" method="POST" class="form form-vertical" role="form" id="frmretornar" >
            <input type="hidden" name="_token" value="{{ csrf_token() }}">      
          <div class="row"> 
            <div class="form-group col-md-4">
              <div class="input-group">
                <div class="input-group-addon"><b>No. REGISTRO</b></div> 
                <input type="hidden" name="mridprestamo" id="mridprestamo">            
                <input type="text" class="form-control" id="mridproducto" name="mridproducto" value="" readonly>       
              </div>            
            </div>
            <div class="form-group col-md-8">
              <div class="input-group">
                <div class="input-group-addon"><b>NOMBRE COMERCIAL</b></div>             
                <input type="text" class="form-control" id="mrnomproducto" name="mrnomproducto" value="" readonly>                 
              </div>
            </div>      
          </div>         
          <div class="row" id="sipres">
            <div class="form-group col-md-6">
              <div class="input-group">
                <div class="input-group-addon"><b>FECHA DE DEVOLUCIÓN</b></div>             
                <input type="text" class="form-control datepicker2" data-date-format="dd-mm-yyyy" id="mrfecha" name="mrfecha" value="">                 
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
