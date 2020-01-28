{{-- */
	$estDet = $est->detalle()->first();
	$estCat = \App\Models\EstablecimientosSI\Establecimientos::findOrFail($estDet->idEstablecimiento);
	$mun = (!empty($estCat->municipio()->first()->nombreMunicipio))?', '.$estCat->municipio()->first()->nombreMunicipio:'';
	$dep = ($mun!='')?', '.$estCat->municipio()->first()->departamento()->first()->nombreDepartamento:'';
	
/* --}}
<div class="row">
	<div class="form-group col-sm-4 col-xs-12">
		<label>Número solicitud</label>
		<input type="text" class="form-control" disabled value="{{ $est->idSolicitud }}">
		<p class="help-block">Número solicitud de establecimientos</p>
	</div>
	<div class="form-group col-sm-4 col-xs-12">
		<label>Código establecimiento</label>
		<input type="text" class="form-control" disabled value="{{ $estCat->idEstablecimiento }}" placeholder="SIN REGISTRAR">
	</div>
	<div class="form-group col-sm-4 col-xs-12">
		<label>Tipo Establecimiento</label>
		<input type="text" class="form-control" disabled value="{{ $estCat->tipo()->first()->nombreTipoEstablecimiento }}">
	</div>
</div><div class="row">
	<div class="form-group col-sm-6 col-xs-12">
		<label>Nombre comercial</label>
		<input type="text" class="form-control" disabled value="{{ $estCat->nombreComercial }}">
	</div>
	<div class="form-group col-sm-6 col-xs-12">
		<label>Tramite</label>
		<input type="text" class="form-control" disabled value="{{ $est->tramite()->first()->nombreTramite }}">
		<p class="help-block">Tramite solicitado en establecimientos</p>
	</div>
	<div class="form-group col-sm-12 col-xs-12">
		<label>Dirección</label>
		<textarea class="form-control" readonly="true">{{ $estCat->direccion.$mun.$dep }}</textarea>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-xs-12">
		<h5 class="small-title"><b>Propietario</b></h5>
		<div class="table-responsive">
			<table class="table table-th-block table-primary">
				<thead>
					<tr><th>NIT</th><th>Nombre</th><th>Teléfonos</th><th>E-mail</th></tr>
				</thead>
				<tbody>
				@foreach($estCat->propietarios()->get() as $r)
					<tr>
						<td>{{ $r->nitPropietario }}</td>
						<td>{{ (empty($r->propietario->nombre))?'':$r->propietario->nombre }}</td>
						{{-- */
						
							$telefonosContacto = (empty($r->propietario->telefonosContacto))?'':$r->propietario->telefonosContacto;
							$telefonosContacto = str_replace('{', '', $telefonosContacto);
							$telefonosContacto = str_replace('}', '', $telefonosContacto);
							$telefonosContacto = str_replace('"telefono1":', '', $telefonosContacto);
							$telefonosContacto = str_replace('"telefono2":', '', $telefonosContacto);	
						
							$tel = json_decode($telefonosContacto);
							
						 /*--}}
						<td>{{ $tel[0].' '.(empty($tel[1])?'':' / '.$tel[1]) }}</td>
						<td>{{ (empty($r->propietario->emailsContacto))?'': $r->propietario->emailsContacto}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div><!-- /.table-responsive -->
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-xs-12">
		<h5 class="small-title"><b>Regente</b></h5>
		<div class="table-responsive">
			<table class="table table-th-block table-primary">
				<thead>
					<tr><th>Inscripción JVPQF</th><th>Nombre</th><th>Teléfonos</th><th>E-mail</th></tr>
				</thead>
				<tbody>
				@foreach($estCat->regentes()->get() as $r)
					<tr>
						<td>{{ $r->idProfesional }}</td>
						<td>{{ (empty($r->regente->nombre))?'':$r->regente->nombre }}</td>
						{{-- */
						
							$telefonosContacto = (empty($r->regente->telefonosContacto))?'':$r->regente->telefonosContacto;
							$telefonosContacto = str_replace('{', '', $telefonosContacto);
							$telefonosContacto = str_replace('}', '', $telefonosContacto);
							$telefonosContacto = str_replace('"telefono1":', '', $telefonosContacto);
							$telefonosContacto = str_replace('"telefono2":', '', $telefonosContacto);	
						
							$tel = json_decode($telefonosContacto);
							
						 /*--}}
						<td>{{ $tel[0].' '.(empty($tel[1])?'':' / '.$tel[1]) }}</td>
						<td>{{ (empty($r->regente->emailsContacto))?'':$r->regente->emailsContacto }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div><!-- /.table-responsive -->
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-xs-12">
		<h5 class="small-title"><b>Actividades a las que se dedica</b></h5>
		<div class="table-responsive">
			<table class="table table-th-block table-primary">
				<tbody>
				@foreach($estCat->actividades()->get() as $r)
					<tr>
						<td>{{ $r->actividad->nombreActividad }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div><!-- /.table-responsive -->
	</div>
</div>