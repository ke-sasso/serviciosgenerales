<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Unidades extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.unidades';
    protected $primaryKey = 'idUnidad';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
	
	public static function getUnidadByIdEmpleado($idEmpleado){
		if($idEmpleado!=null){
			$unidad=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
					->select('uni.*')
					->join('dnm_rrhh_si.RH.plazasFuncionales as func','emp.idPlazaFuncional','=','func.idPlazaFuncional')
					->join('dnm_rrhh_si.RH.unidades as uni','func.idUnidad','=','uni.idUnidad')
					->where('emp.idEmpleado',$idEmpleado)
					->first();
			if($unidad!=null){
				
				return $unidad;
			}
			else{
				return null;
			}
		}	
	}

	public function plazasFuncionales(){
		return $this->hasMany('App\PlazasFuncionales','idUnidad','idUnidad');
	}
}
