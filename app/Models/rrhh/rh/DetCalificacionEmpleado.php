<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class DetCalificacionEmpleado extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.detalleCalificacionEmpleado';
    protected $primaryKey = 'idCorrelativo';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';


	protected function getDateFormat()
	{
		return 'Y-m-d';
	}

}
