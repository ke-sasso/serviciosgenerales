
<!-- Modal para busqueda del Establecimiento Origen-->
<div class="modal fade modal-center" id="mdlprestamoproducto"  tabindex="-2" role="dialog" >
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-success">
                <button type="button" class="close" 
                   data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="frmModalLabel">
                    PRESTAMO EXPEDIENTE DE PRODUCTO
                </h4>
            </div>    
    </br>                
        <!-- Modal Body -->
      <div class="modal-body">
        <form action="{{route('exp.prod.save.prestamo')}}" method="POST" class="form form-vertical" role="form" id="frmprestamo">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">      
          <div class="row"> 
            <div class="form-group col-md-12">
              <div class="input-group">
                <div class="input-group-addon"><b>No. REGISTRO</b></div>             
                <input type="text" class="form-control" id="idproducto" name="idproducto" value="" readonly>       
              </div>            
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 form-group">              
                <label><b>NOMBRE COMERCIAL</b></label>             
                <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" value="" readonly>           
            </div>
          </div>                
          <div id="sipres">       
            <div class="row">
              <div class="form-group col-md-8">
                <div class="input-group">
                  <div class="input-group-addon"><b>FECHA</b></div>             
                  <input type="text" class="form-control datepicker2" data-date-format="dd-mm-yyyy" id="fecha" name="fecha" value="">                 
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <div class="input-group">
                  <div class="input-group-addon"><b>TIPO DE PRÉSTAMO</b></div>             
                    <select class="form-control" name="tipoprestamo">
                      <option value="2">TRAMITE</option>                    
                      <option value="1">CONSULTA</option>                                      
                    </select>              
                </div>
              </div>
            </div> 
          </div>            
          <div class="row" id="nopres">
            <div class="form-group col-md-8">
              <div class="input-group">
                <div class="input-group-addon"><b>ESTADO</b></div>             
                <input type="text" class="form-control" id="estado" value="" readonly>                 
              </div>
            </div>
            <div class="form-group col-md-12">
              <div class="input-group">
                <div class="input-group-addon"><b>EMPLEADO</b></div>             
                <input type="text" class="form-control" id="esolicita" value="" readonly>                 
              </div>
            </div>
            <div class="form-group col-md-12">
              <div class="input-group">
                <div class="input-group-addon"><b>UNIDAD</b></div>             
                <input type="text" class="form-control" id="usolicita" value="" readonly>                 
              </div>
            </div>            
          </div>               
          <div class="row" style="margin-left: 5%; display: inline-block;"><br><br><br>              
              <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success" id="btnprestarEProducto">GUARDAR</button>
                <button type="button" class="btn btn-default" id="btncloseprestar"
                  data-dismiss="modal" style="border: 1px solid black; display: inline-block;">
                      Cerrar
                </button>
              </div>
              
          </div>        
          
        </form>  
         
      </div>
        <!-- End Modal Body -->      
      
      </div>
    </div>
</div>
