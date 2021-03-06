<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

class TareasConocimientos extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultadosFuncionesTareasConocimientos';
    protected $primaryKey = ['idResultado','idConocimiento','idTarea'];
	public $incrementing = false;
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}
}
