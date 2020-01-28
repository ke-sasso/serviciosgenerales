<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PlazasFuncionales;
use DB;
class CatEmpleados extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.empleados';
    protected $primaryKey = 'idEmpleado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
	
	public static function getEmpleadosByUnidad($idUnidad){
		if($idUnidad!=null){
			
			$idPlazas=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.plazasFuncionales')
					->select('idPlazaFuncional')
					->where('idUnidad',$idUnidad)
					->orderBy('idPlazaFuncional')
					->get();
		
		
			$idPlazasF=[];
			if(count($idPlazas)>0){
				for($i=0;$i<count($idPlazas);$i++) {
					$idPlazasF[$i]=$idPlazas[$i]->idPlazaFuncional;
				}
			}
			
			$empleados=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
						->whereIn('idPlazaFuncional',$idPlazasF)
						->get();
		
			/*
			$empleados=CatEmpleados::whereIn('idPlazaFuncional',function($query){
				$query->select('idPlazaFuncional')
				->from(with(new PlazasFuncionales)->getTable())
				->where('idUnidad',$idUnidad)})->get();
			*/
			return $empleados;
			
			
		}
	}


	public static function getEmpleadosByIdPlazaPadre($idPlazaFuncional){

	 return	DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
		->join('dnm_rrhh_si.RH.plazasFuncionales as plaza','emp.idPlazaFuncional','=','plaza.idPlazaFuncional')
		->where('plaza.idPlazaFuncionalPadre',$idPlazaFuncional)->select('emp.*')->get();
		
	}


	public function User(){
		 return $this->belongsTo('App\User', 'idEmpleado', 'idEmpleado');
	}

}
