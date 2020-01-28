<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

use DB;

class ConocimientosTipos extends Model {

	protected $table = 'dnm_rrhh_si.RH.tiposConocimientos';
    protected $primaryKey = 'idTipoConocimiento';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public static function getCmbData(){
		return ConocimientosTipos::where('activo',1)->orderBy('nombreTipoConocimiento','asc')->select(DB::raw('idTipoConocimiento as idCategoria, nombreTipoConocimiento as nombreCategoria'))->get();
		
	}
}