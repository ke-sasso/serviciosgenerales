<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SolLicencia extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.solicitudLicencia';
    protected $primaryKey = 'idSolLicencia';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';
	//protected $dateFormat = 'M j Y h:i:s:000A';
	
	protected function getDateFormat()
	{
		return 'Y-m-d H:i:s';
	}
	
	//funcion que solo va a retornar lsa solicitudes
	//que tienen que ser aprobadas por el doctor
	public static function getLicenciaDirector(){
		/*
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudLicencia as li')
			->join('dnm_rrhh_si.Permisos.solicitudMotivo AS smo','li.enConcepto','=','smo.idSolMot')		
			->join('dnm_rrhh_si.Permisos.motivos AS mot','smo.idMotivo','=','mot.idMotivo')
			->join('dnm_rrhh_si.Permisos.estadoSolicitud AS est','li.idEstado','=','est.idEstadoSol')
			->where('smo.idSolicitud',2)
			->whereIn('idEstado',[1,2,6])
			->where('enConcepto',30)
			->orWhere('dias','>',90)
			//->orderBy('fechaCreacion','desc')
			->select('li.idSolLicencia','smo.idSolicitud AS idTipo','mot.idMotivo','li.dias', 'mot.nombre AS motivo', 'est.idEstadoSol', 'est.nombre AS nombreEstado', 'li.fechaCreacion', 'li.idUsuarioCrea','li.idEmpleadoCrea');
			*/
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwLicenciasNivel3');
			

	}
		//dnm_rrhh_si.Permisos.vwLicenciasNivel2
	public static function getLicenciasNivel2(){
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwLicenciasNivel2');
	}
}
