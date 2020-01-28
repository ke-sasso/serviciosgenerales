<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class SolSeguro extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.solicitudSeguros';
    protected $primaryKey = 'idSolSeguro';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';
	//protected $dateFormat = 'M j Y h:i:s:000';
	//protected $dateFormat = 'M j Y h:i:s:000A';
	//protected $dateFormat = 'M j Y h:i:s:000A';
	
	protected function getDateFormat()
	{
		return 'Y-m-d H:i:s.000';
	}
	
	public static function getSolicitudesSeguro(){
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudSeguros as se')
		->join('dnm_rrhh_si.Permisos.motivos as mot','se.idMotivo','=','mot.idMotivo')
		->join('dnm_rrhh_si.Permisos.estadoSeguros as est','se.idEstadoSeguro','=','est.idEstadoSeguro')
		//->orderBy('se.fechaCreacion','desc')
		->select('se.idSolSeguro','mot.nombre as motivo','se.fechaCreacion','est.idEstadoSeguro','est.nombreEstado')
		->where('idEmpleadoCrea',Auth::user()->idEmpleado);
	}
		public static function getSegurosDNM(){
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudSeguros as se')
		->join('dnm_rrhh_si.Permisos.motivos as mot','se.idMotivo','=','mot.idMotivo')
		->join('dnm_rrhh_si.Permisos.estadoSeguros as est','se.idEstadoSeguro','=','est.idEstadoSeguro')
		->join('dnm_rrhh_si.RH.empleados as emple', 'se.idEmpleadoCrea','=','emple.idEmpleado')
        ->join('dnm_rrhh_si.RH.plazasFuncionales as plaza','emple.idPlazaFuncional','=','plaza.idPlazaFuncional')
        ->join('dnm_rrhh_si.RH.unidades as uni', 'plaza.idUnidad', '=','uni.idUnidad')
		->select('se.idEstadoSeguro','se.idEmpleadoCrea','se.idUsuarioCrea','se.idSolSeguro','mot.nombre',
			'se.fechaCreacion','est.nombreEstado','uni.idUnidad','uni.nombreUnidad','se.idMotivo');
	}
}	
