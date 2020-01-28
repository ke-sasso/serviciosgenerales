<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class EstadosSeguro extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.estadoSeguros';
    protected $primaryKey = 'idEstadoSeguro';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

}
