<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class Jefes extends Model {

	protected $table = 'dnm_rrhh_si.RH.jefes';
    protected $primaryKey = 'idJefe';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public static function isThisPlazaABoss($idPlazaFuncional){
		if(Jefes::where('idPlazaFuncional',$idPlazaFuncional)->count()>0){
			return true;
		}else{
			return false;
		}
	}

	/*pregunta si el usuario logueado es jefe y si su unidad es propietaria de registros*/
	public static function EsJefeYTienePrestamosAsignados()
	{
		$cuenta = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
              ->join('dnm_rrhh_si.RH.plazasFuncionales as pla','emp.idPlazaFuncional','=','pla.idPlazaFuncional')
              ->join('dnm_rrhh_si.RH.jefes as jf','pla.idPlazaFuncional','=','jf.idPlazaFuncional')
              ->join('dnm_rrhh_si.RH.unidades as uni','pla.idUnidad','=','uni.idUnidad')
              ->join('dnm_ugda_si.PRES.cat_tipo_registro_unidad as tu','uni.idUnidad','=','tu.idUnidad')
              ->where('emp.idEmpleado',Auth::user()->idEmpleado)            
              ->count();
        return $cuenta;
	}

}