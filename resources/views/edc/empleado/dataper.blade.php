 <div class="row">
    <div class="form-group col-sm-12 col-xs-12">
        <label>Nombre Completo</label>
        <input type="text" class="form-control" disabled value="{{ $emp->getNombreCompleto() }}">
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6 col-xs-12">
        <label>Género</label>
        <input type="text" class="form-control" disabled value="{{$emp->getTextoGenero()}}">
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12 col-xs-12">
        <label>Unidad</label>
        <input type="text" class="form-control" disabled value="{{ $emp->getTextoNombreUnidad() }}">
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12 col-xs-12">
        <label>Plaza Funcional</label>
        <input type="text" class="form-control" disabled value="{{ $emp->getTextoNombrePlazaFuncional() }}">
    </div>
</div>