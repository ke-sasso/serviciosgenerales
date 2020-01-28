@extends('master')
@section('contenido')
<div class="container-fluid">
  <div class="row">
    
    <div class="col-sm-4">
      <div class="the-box no-border bg-warning tiles-information">
        <i class="fa fa-line-chart icon-bg"></i>
        <div class="tiles-inner text-center">
          <a href="{{ route('rh.capacitaciones.desempenios') }}" class="btn btn-warning btn-lg">DESEMPEÑOS</a>
          <div class="progress no-rounded progress-xs">
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            </div><!-- /.progress-bar .progress-bar-info -->
          </div><!-- /.progress .no-rounded -->
          <p><small></small></p>
        </div><!-- /.tiles-inner -->
      </div><!-- /.the-box no-border -->
    </div><!-- /.col-sm-4 -->
              
    <div class="col-sm-4">
      <div class="the-box no-border bg-primary tiles-information">
        <i class="fa fa-line-chart icon-bg"></i>
        <div class="tiles-inner text-center">
          <a href="{{ route('rh.capacitaciones.productos') }}" class="btn btn-primary btn-lg">PRODUCTOS</a>
          <div class="progress no-rounded progress-xs">
            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            </div><!-- /.progress-bar .progress-bar-info -->
          </div><!-- /.progress .no-rounded -->
          <p><small></small></p>
        </div><!-- /.tiles-inner -->
      </div><!-- /.the-box no-border -->
    </div><!-- /.col-sm-4 -->

  </div><div class="row">

    <div class="col-sm-4">
      <div class="the-box no-border bg-success tiles-information">
        <i class="fa fa-line-chart icon-bg"></i>
        <div class="tiles-inner text-center">
          <a href="{{ route('rh.capacitaciones.conocimientos') }}" class="btn btn-success btn-lg">CONOCIMIENTOS</a>
          <div class="progress no-rounded progress-xs">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
              </div><!-- /.progress-bar .progress-bar-info -->
          </div><!-- /.progress .no-rounded -->
          <p><small></small></p>
        </div><!-- /.tiles-inner -->
      </div><!-- /.the-box no-border -->
    </div><!-- /.col-sm-4 -->

    <div class="col-sm-4">
      <div class="the-box no-border bg-info tiles-information">
      <i class="fa fa-line-chart icon-bg"></i>
      <div class="tiles-inner text-center">
        <a href="{{ route('rh.capacitaciones.actitudes') }}" class="btn btn-info btn-lg">ACTITUDES</a>
        <div class="progress no-rounded progress-xs">
          <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
          </div><!-- /.progress-bar .progress-bar-info -->
        </div><!-- /.progress .no-rounded -->
        <p><small></small></p>
      </div><!-- /.tiles-inner -->
      </div><!-- /.the-box no-border -->
    </div><!-- /.col-sm-4 -->

  </div>
</div>
@endsection