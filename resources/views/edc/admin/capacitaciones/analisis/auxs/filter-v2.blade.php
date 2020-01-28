<div id="collapse-filter" class="collapse" style="height: 0px;">
  <div class="panel-body " >
      {{-- COLLAPSE CONTENT --}}
      <form role="form" id="search-form">
        <div class="row">
          <div class="form-group col-md-11">
            <label>Categorías:</label>
            <select class="form-control chosen-select" multiple name="categoria" id="categoria" placeholder="Seleccione una o varias unidades">
                  @if(!empty($categorias))
                     @foreach($categorias as $categoria)
                        <option value="{{ $categoria->idCategoria }}">{{ $categoria->nombreCategoria }}</option>
                     @endforeach
                  @endif
            </select>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-12">
            <label>Unidad:</label>
            <select class="form-control chosen-select" multiple name="unidad" id="unidad" placeholder="Seleccione una o varias unidades">
                  @if(!empty($unidades))
                     @foreach($unidades as $unidad)
                        <option value="{{ $unidad->idUnidad }}">{{ $unidad->nombreUnidad }} ({{ $unidad->prefijo }})</option>
                     @endforeach
                  @endif
            </select>
          </div>
      </div><div class="row">
          <div class="form-group col-md-12">
            <label>Plaza Funcional:</label>
              <select class="form-control chosen-select"" name="plazaFuncional"  multiple id="plazaFuncional" >
                    @if(!empty($funcionales))
                       @foreach($funcionales as $funcional)
                          <option value="{{ $funcional->idPlazaFuncional }}">{{ $funcional->nombrePlaza }}</option>
                       @endforeach
                    @endif
              </select>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-6">
                  <label>Estado:</label>
                  <select class="form-control" name="estado" id="estado" >
                        @if(!empty($estados))
                           @foreach($estados as $estado)
                              <option value="{{ $estado->idEstado }}">{{ $estado->nombreEstado }}</option>
                           @endforeach
                        @endif
                  </select>
              </div>

              <div class="form-group col-md-6">
                  <label>Evaluaciones:</label>
                  <select class="form-control" name="evaluacion" id="evaluacion" >
                        <option value="-1">Todas</option>
                        @if(!empty($evaluaciones))
                           @foreach($evaluaciones as $eva)
                              <option value="{{$eva->idEvaluacion}}">{{$eva->nombre}} ({{$eva->periodo}})</option>
                           @endforeach
                        @endif
                  </select>
              </div>

        </div>

        <div class="modal-footer" >
          <div align="center">
             <input type="hidden" name="_token" value="{{ csrf_token() }}" class="form-control"/>
            <button type="submit" class="btn btn-success btn-perspective"><i class="fa fa-search"></i> Buscar</button>
          </div>
        </div>
      </form>
      {{-- /.COLLAPSE CONTENT --}}
  </div><!-- /.panel-body -->
</div><!-- /.collapse in -->