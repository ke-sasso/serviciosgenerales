<!-- BEGIN TOP NAV -->
<div class="top-navbar {{ (Session::get('cfgHideMenu',false))?'toggle':'' }}">
	<div class="top-navbar-inner">
		
		<!-- Begin Logo brand -->
		<div class="logo-brand success-color">
			<a>{!! Html::image('img/logo.png') !!}</a>
		</div><!-- /.logo-brand -->
		<!-- End Logo brand -->
		
		<div class="top-nav-content">
			
			<!-- Begin button sidebar left toggle -->
			<div class="btn-collapse-sidebar-left">
				<i class="fa fa-long-arrow-right icon-dinamic {{ (Session::get('cfgHideMenu',false))?'rotate-180':'' }}" onclick="changeConfigMenu()"></i>
			</div><!-- /.btn-collapse-sidebar-left -->
			<!-- End button sidebar left toggle -->
						
		</div><!-- /.top-nav-content -->

		<!-- Begin user session nav -->
		<ul class="nav-user navbar-right">
			<li class="dropdown">
			  <a href="#fakelink" class="dropdown-toggle" data-toggle="dropdown">
				Hola, <strong>{{ Auth::user()->nombresUsuario.' '.Auth::user()->apellidosUsuario }}</strong>
			  </a>
			  <ul class="dropdown-menu square primary margin-list-rounded with-triangle">
				<li><a href="{{ url('/logout') }}">Cerrar Sesión</a></li>
			  </ul>
			</li>
		</ul>
		<!-- End user session nav -->

	</div><!-- /.top-navbar-inner -->
</div><!-- /.top-navbar -->
<!-- END TOP NAV -->