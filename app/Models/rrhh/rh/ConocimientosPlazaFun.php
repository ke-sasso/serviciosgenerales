<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class ConocimientosPlazaFun extends Model {


	protected $table = 'dnm_rrhh_si.RH.conocimientoPlazaFuncional';
    protected $primaryKey = 'idItem';
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function actitud(){
		return $this->hasOne('App\Models\rrhh\rh\ActitudesTipos','idTipoActitud','idTipoActitud');
	}

}
