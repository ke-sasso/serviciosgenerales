<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;

use DB;

class Evaluaciones extends Model {

	//
	protected $table = 'dnm_rrhh_si.EDC.evaluaciones';
    protected $primaryKey = 'idEvaluacion';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public static function getEvaluacionVigente(){
		return Evaluaciones::where('activo',1)->where('idEvaluacion','<>',5)->where('fechaInicio','<=',date('Y-m-d'))->where('fechaFin','>=',date('Y-m-d'))->first();
	}


	public static function getDataHistorial($idEmp = null){
		if(empty($idEmp)){
			DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwEvaluacionesHistorial as eva');
			/*
			return DB::connection('sqlsrv')->table('dnm_rrhh_si.EDC.evaluaciones as e')
				->join('dnm_rrhh_si.EDC.resultados as r','r.idEvaluacion','=','e.idEvaluacion')
				->join('dnm_rrhh_si.EDC.resultadosEstados as re','re.idEstado','=','r.idEstado')
				->join('dnm_rrhh_si.RH.empleados as emp','emp.idEmpleado','=','r.idEmpleado')
				->join('dnm_rrhh_si.RH.plazasFuncionales as pf','pf.idPlazaFuncional','=','r.idPlazaFuncional')
				->where('e.activo',0)->orderBy('e.idEvaluacion')
				->select(['e.idEvaluacion','e.nombre','e.periodo', 'emp.nombresEmpleado', 'emp.apellidosEmpleado','pf.nombrePlaza','r.idResultado','re.nombreEstado','r.CP','r.sumTotales','r.sumParciales','r.sumMinimas']);
				*/
		}else{
			DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwEvaluacionesHistorial as eva')->where('idEmpleado',$idEmp);
			/*
			return DB::connection('sqlsrv')->table('dnm_rrhh_si.EDC.evaluaciones as e')
				->join('dnm_rrhh_si.EDC.resultados as r','r.idEvaluacion','=','e.idEvaluacion')
				->join('dnm_rrhh_si.EDC.resultadosEstados as re','re.idEstado','=','r.idEstado')
				->join('dnm_rrhh_si.RH.empleados as emp','emp.idEmpleado','=','r.idEmpleado')
				->join('dnm_rrhh_si.RH.plazasFuncionales as pf','pf.idPlazaFuncional','=','r.idPlazaFuncional')
				->where('e.activo',0)->where('r.idEmpleado',$idEmp)->orderBy('e.idEvaluacion')
				->select(['e.idEvaluacion','e.nombre','e.periodo', 'emp.nombresEmpleado', 'emp.apellidosEmpleado','pf.nombrePlaza','r.idResultado','re.nombreEstado','r.CP','r.sumTotales','r.sumParciales','r.sumMinimas']);
				*/
		}
	}
}
