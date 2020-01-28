<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;
use DB;
class CapacitacionesDetalle extends Model {

	protected $table = 'dnm_rrhh_si.RH.detalleCapacitaciones';
    protected $primaryKey = 'idDetalle';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';

	protected $fillable = ['idCapacitacion', 'idResultado','idProducto','idDesempenio','idActitud','idConocimiento'];
	
	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}
	
}
