{{-- HISTORIAL --}}
<ul class="timeline">
	<li class="centering-line"></li>
	
	<!-- BEGIN TIME CAT-->
	<li class="center-timeline-cat">
		<div class="inner">
			Actualmente
		</div><!-- /.inner -->
	</li>
</ul>
<ul class="timeline">
	<li class="centering-line"></li>
	@foreach($soli->getDataHistorialByIdSolicitudEstablecimiento($soli->id_solicitud_establecimiento) as $h)
		<li class="item-timeline">
			<div class="buletan"></div>
			<div class="inner-content">
				
				<div class="heading-timeline">
					<div class="user-timeline-info">
						<p>{{ date_format(date_create($h->fecha_creacion),'Y-m-d H:i') }}<small>{{ $h->usuario_creacion }} ({{ $h->unidad }})</small></p>
					</div><!-- /.user-timeline-info -->
				</div><!-- /.heading-timeline -->
				<p><b>{{ $h->nombre_actividad }}</b><br/><small>{!!  $h->observaciones !!}</small></p>
			</div><!-- /.inner-content -->
		</li>
		
	@endforeach
	<li class="centering-line"></li>
	
	<li class="item-timeline highlight text-center">
		<div class="buletan"></div>
		<div class="inner-content">
			<h2 class="text-primary"><i class="fa fa-flag-checkered"></i></h2>
			<h4>Inicio de proceso</h4>
		</div><!-- /.inner-content -->
	</li>
	
</ul>	
<!-- END TIMELINE -->
{{-- /HISTORIAL --}}