<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

use DB;

class ActitudesTipos extends Model {

	protected $table = 'dnm_rrhh_si.RH.tiposActitudes';
    protected $primaryKey = 'idTipoActitud';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public static function getCmbData(){
		return ActitudesTipos::where('activo',1)->orderBy('nombreTipoActitud','asc')->select(DB::raw('idTipoActitud as idCategoria, nombreTipoActitud as nombreCategoria'))->get();
		
	}
}