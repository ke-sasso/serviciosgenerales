@extends('master')
@section('css')
<style type="text/css">
.entry:not(:first-of-type)
{
    margin-top: 10px;
}

.glyphicon
{
    font-size: 12px;
}
.text-uppercase
{ text-transform: uppercase; }
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
        <strong>Auchh!</strong>
        Algo ha salido mal. {{ Session::get('msnError') }}
    </div>
@endif
<div class="the-box">
            <h4 class="small-title">Edicion Solicitud de Transporte</h4>
                            
            {!!Form::open(['route' => ['transporte.asignar'], 'method' => 'POST'])!!}
                                    
                                    <div class="form-group">
                                        {!! Form::hidden('idSolicitud', Crypt::encrypt($trpsolicitud->idSolicitud)) !!}
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                            {!! Form::label('fechaSolicitud', 'Fecha de Solicitud:') !!}
                                            {!! Form::text('fechaCreacion',date_format(date_create($trpsolicitud->fechaTransporte),'d-m-Y'),['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy', 'disabled'])!!}


                                            </div><!-- /.form-group -->
                                        </div><!-- /.col-sm-3 -->


                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        {!! Form::label('fechaTransporte', 'Fecha de Transporte:') !!}
                                        {!! Form::text('fechaTransporte',date_format(date_create($trpsolicitud->fechaTransporte),'d-m-Y'),['id'=>'fechaSolicitudD','class' => 'form-control datepicker','data-date-format'=>'dd-mm-yyyy','placeholder'=>'dd-mm-yyyy', 'disabled'])!!}


                                        </div><!-- /.form-group -->
                                    </div><!-- /.col-sm-3 -->

                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        {!! Form::label('horaInicio', 'Hora Inicio') !!}
                                            <div class="input-group input-append bootstrap-timepicker">
                                                {!! Form::text('horaInicio',date_format(date_create($trpsolicitud->horaInicio),'h:i A'),['id'=>'timepicker1','class' => 'form-control timepicker ', 'disabled'])!!}
                                                <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                            </div>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col-sm-6 -->
                                    @if($trpsolicitud->horaFin!=null)
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                        {!! Form::label('horaFin', 'Hora Fin') !!}
                                          
                                                <div class="input-group input-append bootstrap-timepicker">
                                                    {!! Form::text('horaFin',date_format(date_create($trpsolicitud->horaFin),'h:i A'),['id'=>'timepicker1','class' => 'form-control timepicker ', 'disabled'])!!}
                                                    <span class="input-group-addon add-on"><i class="fa fa-clock-o"></i></span>
                                                </div>
                                           
                                           
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col-sm-6 -->
                                     @endif
                                    
    
                                        
                                </div>
                                    <div class="form-group">
                                            {!! Form::label('lugar', 'Lugar') !!}
                                            {!! Form::text('lugar',$trpsolicitud->lugar,['class' => 'form-control text-uppercase', 'disabled'])!!}
                                    </div>

                                    <div class="form-group">
                                            {!! Form::label('persona', 'Personas a Transportar') !!}
                                        
                                            <div class="form-group">
                                                
                                                @for($i=0;$i<count($personas);$i++)
                                                <div class="row">    
                                                <div class="col-sm-6 col-md-6">
                                                <input type="text" name="idDetalleP" value="{{$personas[$i]}}" class="form-control" disabled>
                                                </div>
                                                </div>
                                                @endfor
                                                
                                            </div>
                                        
                                    </div>

                                    
                                    <div class="form-group">
                                            {!! Form::label('descripcion', 'Descripcion') !!}
                                            
                                            {!! Form::text('descripcion',$trpsolicitud->descripcion,['class' => 'form-control','disabled'])!!}
                                            
                                    </div>

                                    
                                    

                                     <div class="row">
                                        
                                            <div class="col-sm-3">
                                                <div class="form-group">
												 @if($trpsolicitud->conMotorista==1)
													 {!! Form::label('motorista', 'Nombre del Motorista:') !!}
										
													  <select class="form-control" name="idMotorista" id="selectitem" required>
														<option value="" disabled selected>Seleccione un Motorista
														  </option>
                                                    @foreach($motoristas as $motorista)
                                                      <option value="{{$motorista->idMotorista}}">{{$motorista->motorista}}
                                                      </option>
                                                   @endforeach
                                                  </select>
												  @else
													 {!! Form::label('motorista', 'Nombre del Motorista:') !!}
										
													  <select class="form-control" name="idMotorista" id="selectitem" required>
														<option value="" disabled selected>Seleccione un Motorista
														  </option>
                              
                                                  </select> 
												@endif
                                               

                                                </div><!-- /.form-group -->
                                            </div><!-- /.col-sm-6 -->
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                <label>Seleccione un Vehiculo</label>
                                                <select class="form-control" name="idVehiculo" id="selectveh" required>
                                                    <option value="" disabled selected>Seleccione un Vehiculo
                                                    @foreach($catvehiculos as $catvehiculo)
                                                        <option value="{{$catvehiculo->idVehiculo}}">{{$catvehiculo->numPlaca}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                </div><!-- /.form-group -->
                                            </div>
                                            
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                {!! Form::label('marca', 'Marca:') !!}
                                                {!! Form::text('marca',null,['id'=>'marcaid','class' => 'form-control' ,'disabled'])!!}
                                                 
                                                </div><!-- /.col-sm-6 -->
                                            </div>

                                           <div class="col-sm-3">
                                                <div class="form-group">
                                                {!! Form::label('tipo', 'Tipo de Vehiculo:') !!}
                                                {!! Form::text('modelo',null,['id'=>'modeloid','class' => 'form-control' ,'disabled'])!!}
                                               
                                        
                                                </div><!-- /.form-group -->
                                            </div><!-- /.col-sm-6 -->
                                            

                                    </div>
									
									<div class="row">
                                        
                                            <div class="col-sm-3">
                                                <div class="form-group">
										
											<label>Seleccione cuantos vales de gasolina:</label>
												<select class="form-control" name="vales" id="vales" required>
													<option value="" disabled selected>Seleccione...
													</option>
													<option value="1">1 (UNO)
													</option>
													<option value="2">2 (DOS)
													</option>
													<option value="3">3 (TRES)
													</option>
													<option value="4">4 (CUATRO)</option>
													<option value="5">5 (CINCO)</option>
													<option value="6">6 (SEIS)</option>
													<option value="7">7 (SIETE)</option>
													<option value="8">8 (OCHO)</option>
													<option value="9">9 (NUEVE)</option>
													<option value="10">10 (DIEZ)</option>
												</select>
										</div>
									</div>
									</div>
									<br>

								  <div align="center" class="from-group">
								  {!!Form::submit('Guardar', ['class' => 'btn btn-primary'])!!}
								  </div>
                                               
                        {!!Form::close()!!}
                        
    
</div>
@endsection
@section('js')
<script type="text/javascript">

    

    var array = {!!json_encode($catvehiculos)!!};
    $('#selectveh').on('change',function(){
        valor = $(this).val();
         console.log(valor);
        for(i=0;i<array.length;i++){
            if(array[i].idVehiculo == valor){
                console.log(array[i].idVehiculo);
                document.getElementById("marcaid").value=array[i].marca;
                document.getElementById("modeloid").value=array[i].tipo;
               // document.getElementById("motoid").value=array[i].motorista;

            }
    }
     
    });

    $('#selectitem').on('change', function(){
        //console.log($(this).val());
        var idMotorista = $(this).val();
 
        $.get("{{ url('information') }}/create/ajax-state?idMotorista=" + idMotorista, 
            function(data) {
           // console.log(data.length);
           
            $.each(data, function(index,subCatObj){
                    //$('#idvehiculo').append("<option value='"+subCatObj.idVehiculo  +"'>" + subCatObj.numPlaca + "</option>");
                     $('#selectveh option[value=subCatObj.idVehiculo]').prop('selected', true);
                    for(i=0;i<array.length;i++){
                        if(array[i].idVehiculo == subCatObj.idVehiculo){
                            console.log(subCatObj.idVehiculo);
                            document.getElementById("marcaid").value=array[i].marca;
                            document.getElementById("modeloid").value=array[i].modelo;
                           // document.getElementById("motoid").value=array[i].motorista;
                        }
                    }

            });
            
            
        });
    });    
$(document).ready(function () {
     $('#form').submit(function(event) {
            
        $.ajax({
            data:  $('#form').serialize(),
            url:   "{{ route('solicitudes.est.confirmaciones.guardar') }}",
            type:  'post',
            dataType: "json",
            beforeSend: function() {
                $('body').modalmanager('loading');
            },
            success:  function (r){
                $('body').modalmanager('loading');
                if(r.status == 200){
                  if(r.data==='0'){
                      alertify.alert("Mensaje de sistema",r.message);
                    window.location.href = '{{route("solicitudes.est")}}';
                  }
                  else {
                   alertify.alert("Mensaje de sistema",r.message);
                     alertify.confirm('Nueva Solicitud', 'Desea agregar una nueva Solicitud de Transporte para su regreso?', 
                              function(){ alertify.success('SI'), window.location.href = '';  
                                 }, function(){ alertify.error('NO'),window.location.href = '';});
                  }
                }
                else if (r.status == 400){
                    alertify.alert("Mensaje de sistema - Error",r.message);
                }else if(r.status == 401){
                    alertify.alert("Mensaje de sistema",r.message, function(){
                        window.location.href = r.redirect;
                    });
                }else{//Unknown
                    alertify.alert("Mensaje de sistema - Error", "Oops!. Algo ha salido mal, contactar con el adminsitrador del sistema para poder continuar!");
                    console.log(r);
                }
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
        return false;

        
    });
	
	
 });

$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><input type="text" name="mytext[]"/><a href="#" class="remove_field">Eliminar</a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

        
    
    
   
</script>
@endsection