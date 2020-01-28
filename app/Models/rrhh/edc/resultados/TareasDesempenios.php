<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

class TareasDesempenios extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultadosFuncionesTareasDesempenios';
    protected $primaryKey = ['idResultado','idDesempenio'];
	public $incrementing = false;
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	protected $dateFormat = 'Y-m-d H:i:s';
}
