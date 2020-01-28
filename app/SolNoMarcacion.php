<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SolNoMarcacion extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.solicitudNoMarcacion';
    protected $primaryKey = 'idSolNoMarca';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';
	//protected $dateFormat = 'Y-m-d H:i:s';
	//protected $dateFormat = 'M j Y h:i:s:000A';
	
	protected function getDateFormat()
	{
		return 'Y-m-d H:i:s.000';
	}

}
