{{-- */
	$permisos = App\UserOptions::getAutUserOptions();
	$eva = App\Models\rrhh\edc\Evaluaciones::getEvaluacionVigente();
	$esjefe = App\Models\rrhh\rh\Jefes::EsJefeYTienePrestamosAsignados();
/*--}}

<!-- BEGIN SIDEBAR LEFT -->
<div class="sidebar-left sidebar-nicescroller {{ (Session::get('cfgHideMenu',false))?'toggle':'' }}">
	<ul class="sidebar-menu">
		<li class="{{ (Request::is('inicio') || Request::is('/')) ? 'active selected' : '' }}">
			<a href="{{ url('/inicio') }}"><i class="fa fa-dashboard icon-sidebar"></i>Inicio</a>
		</li>
		
		@if(in_array(444, $permisos, true))
		

        <li class="dropdown">
			<a href="#fakelink">
				<i class="fa fa-folder-open icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Solicitudes Transporte
				</a>
				<ul class="submenu">
					@if(in_array(443, $permisos, true))
					<li class="{{ Request::is('solicitudes/transporte*') ? 'active selected' : '' }}">
						<a href="{{ route('solicitudes.est') }}">Solicitudes de Transporte</a>
					</li>
					@endif
					@if(in_array(450, $permisos, true))
					<li>
						<a href="{{ route('solicitudes.unidad.transporte') }}">Solicitudes de la Unidad</a>
					</li>
					@endif
					
					@if(in_array(444, $permisos, true))
					<li class="{{ Request::is('solicitudes/transporte*') ? 'active selected' : '' }}">
						<a href="{{ route('nuevasolicitud') }}">Nueva Solicitud de Transporte</a>
					</li>
					@endif
					
				</ul>

		</li>
		@endif
		@if(in_array(442,$permisos,true))
		<li>
			<a href="{{route('histMarcaciones')}}">
				<i class="fa fa-calendar icon-sidebar" aria-hidden="true"></i>
				Marcaci&oacute;n
			</a>
		</li>
		@if((App\Models\rrhh\rh\Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->esJefatura()))
			<li>
				<a href="{{route('marcacion.empleados')}}">
					<i class="fa fa-calendar icon-sidebar" aria-hidden="true"></i>
					Marcaci&oacute;n Empleados
				</a>
			</li>
		@endif
        @endif
        @if(in_array(442, $permisos, true))
			<li class="dropdown">
			<a href="#fakelink">
				<i class="fa fa-envelope-o icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Permisos y Seguro
				</a>
				<ul class="submenu">
					
					<li>
						<a href="{{ route('nomarcacion') }}">Nueva Solicitud de NO Marcacion</a>
					</li>
					<li>
						<a href="{{ route('licencia') }}">Nueva Solicitud de licencia</a>
					</li>
					<li>
						<a href="{{ route('seguro') }}">Nueva Solicitud de Seguro</a>
					</li>
					<li>
						<a href="{{ route('all.permisos') }}">Solicitudes de Permisos</a>
					</li>
					<li>
						<a href="{{ route('all.seguros') }}">Solicitudes de Seguro</a>
					</li>
					
				@if(Auth::user()->idEmpleado!=0 or Auth::user()->idEmpleado!=null)
					@if(App\CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20 || App\CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==19 || App\CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==25)
						<li>
							<a href="{{route('all.licencias.director')}}">Licencias a Autorizar</a>
						</li>
					@endif
				@endif
					@if(in_array(450, $permisos, true))
					<li>
						<a href="{{ route('all.permisos.unidad') }}">Solicitudes de la Unidad</a>
					</li>

					@endif					
					@if(in_array(452, $permisos, true))
					<li>
						<a href="{{ route('all.permisos.dnm') }}">Solicitudes de Licencia Autorizadas</a>

					@endif
				   @if(in_array(452, $permisos, true))
					<li>
						<a href="{{ route('all.seguros.dnm') }}">Lista de solicitudes de seguros</a>

					@endif
				</ul>
		@endif
        
		@if(in_array(455, $permisos, true))
        <li class="{{ Request::is('perfiles*') ? 'active selected' : '' }}">
			<a href="#fakelink">
				<i class="fa fa-address-card-o icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Perfiles de puesto
				</a>
				<ul class="submenu {{ Request::is('perfiles*') ? 'visible' : '' }}">
					<li class="{{ Request::is('perfiles*') ? 'active selected' : '' }}">
						<a href="{{ route('perfiles.puesto') }}">Ver </a>
					</li>
					
				</ul>
		</li>
		@endif

		<li class="{{ Request::is('edc*') ? 'active selected' : '' }}">
			<a href="#fakelink">
				<i class="fa fa-area-chart icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Evaluaciones desempeño
				</a>
				<ul class="submenu {{ Request::is('edc*') ? 'visible' : '' }}">
					@if(in_array(455, $permisos, true))
					<li class="{{ Request::is('edc/rh*') ? 'active selected' : '' }}">
						<a href="{{ route('edc.rh.admin') }}">Evaluaciones Finalizadas</a>
					</li>
					@endif
					@if(!empty($eva))

							<li class="{{ Request::is('edc/empleado') ? 'active selected' : '' }}">
								<a href="{{ route('edc.admin') }}">{{ $eva->nombre.' ('.$eva->periodo.')' }}</a>
							</li>

					@endif
					@if((App\Models\rrhh\rh\Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->esJefatura()))
					<li class="{{ Request::is('edc/empleado/PersonalEnPruebas') ? 'active selected' : '' }}">
						<a href="{{ route('edc.admin.pruebas') }}">Personal En Pruebas</a>
					</li>
					@endif
					<li class="{{ Request::is('edc/historial*') ? 'active selected' : '' }}">
						<a href="{{ route('edc.historial') }}">Historial</a>
					</li>					
				</ul>
		</li>

		<li class="{{ Request::is('training*') ? 'active selected' : '' }}">

			<a href="#fakelink">
				<i class="fa fa-object-group icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Capacitaciones
				</a>
				<ul class="submenu {{ Request::is('emp*') ? 'visible' : '' }}">
					
					<li class="{{ Request::is('emp/training*') ? 'active selected' : '' }}">
						<a href="{{route('rh.capacitaciones.emp')}}">Mis Capacitaciones</a>
					</li>					
				</ul>
		</li>		

		@if(in_array(455, $permisos, true))
		<li class="{{ Request::is('training*') ? 'active selected' : '' }}">
			<a href="#fakelink">
				<i class="fa fa-object-group icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Plan de Capacitaciones
				</a>
				<ul class="submenu {{ Request::is('training*') ? 'visible' : '' }}">
					
					<li class="{{ Request::is('training/admin*') ? 'active selected' : '' }}">
						<a href="{{route('rh.capacitaciones.admin')}}">Administrador Capacitaciones</a>
					</li>

					<li class="{{ Request::is('training/plan*') ? 'active selected' : '' }}">
						<a href="{{route('rh.capacitaciones.plan')}}">Análisis resultados EDC</a>
					</li>
				</ul>
		</li>
		@endif		
		<li class="dropdown">
 
 			<a href="#fakelink">
 				<i class="fa fa-archive icon-sidebar"></i>
 				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
 				Expedientes
 				</a>
 				<ul class="submenu">
 					
 					<li>
 						<a href="{{route('archivo.inicio')}}">Préstamo de expedientes</a>
 					</li>
 					<li>
 						<a href="{{route('exp.prod.misprestamos')}}">Mis Préstamos</a>	
 					</li>
 					<li>
 						<a href="{{route('misprestamos.historial')}}">Historial de préstamos</a>	
 					</li>
 				
 					<li>
 						<a href="{{route('solicitudes.to.autorizar')}}">Autorización de Préstamos</a>
 					</li>
 					<li>
 						<a href="{{route('historial.autorizaciones')}}">Historial de Autorizaciones</a>
 					</li>
 							
 				</ul>
 		</li>
		@if(in_array(442, $permisos, true))
		<li class="{{ Request::is('user*') ? 'active selected' : '' }}">
			<a href="#">
				<i class="fa fa-user icon-sidebar"></i>
				<i class="fa fa-angle-right chevron-icon-sidebar"></i>
				Perfil de Usuario
			</a>
			<ul class="submenu {{ Request::is('user*') ? 'visible' : '' }}">					

					<li class="{{ Request::is('user/passwd*') ? 'active selected' : '' }}">
						<a href="{{route('view.passwd')}}"><i class="fa fa-key"></i>Contraseña de Sistemas</a>
					</li>
				</ul>
		</li>
		@endif

	</ul>
</div><!-- /.sidebar-left -->
<!-- END SIDEBAR LEFT -->
