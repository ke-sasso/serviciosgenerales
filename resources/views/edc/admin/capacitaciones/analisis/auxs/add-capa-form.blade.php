<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Adicionar a capacitación</h3>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-sm-6">
          <div class="form-group">
              <label class='text-warning'>Adicionar todos los items marcados dentro de la capacitación:</label>
          </div>
      </div>
      <div class="col-sm-4">
          <div class="form-group">
              <select class="form-control" name="capacitacion" id="capacitacion" >
                {!! $capacitaciones !!}
              </select>
          </div>
      </div>
      <div class="col-sm-2">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="tipo" value="{{ $tipo }}">
        <button type="submit" class="btn btn-primary btn-perspective"><i class="fa fa-floppy-o"></i> Guardar</button>
      </div>
    </div>
  </div>
</div>