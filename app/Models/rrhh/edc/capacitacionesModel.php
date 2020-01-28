<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;
use DB;

class capacitacionesModel extends Model {

	protected $table = 'dnm_rrhh_si.RH.capacitaciones';
    protected $primaryKey = 'idCapacitacion';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';


	protected function getDateFormat()
	{
		return 'Y-m-d';
	}

	public static function getCmbData(){
		$result = "";
		foreach (capacitacionesModel::where('estado',1)->get() as $r) {
			$result.= "<option value='$r->idCapacitacion'>$r->nombreCapacitacion</option>";
		}
		return $result;
	}

	public static function getInstitucionImparte($idInstitucion)	
	{
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.institucionesEstudios')->where('idInstitucion',$idInstitucion)->get();

	}

}
