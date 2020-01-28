<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

class Estados extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultadosEstados';
    protected $primaryKey = 'idEstado';
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public function scopeActivos(){
		return $this->where('activo',1);
	}
}
