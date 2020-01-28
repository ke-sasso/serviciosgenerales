<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class SolicitudMotivo extends Model {

	//
	
	protected $table = 'dnm_rrhh_si.Permisos.solicitudMotivo';
    protected $primaryKey = 'idSolMot';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';
	
	public static function getMotivosBySolicitud($idSolicitud)
	{	
		if($idSolicitud!=null){
		
		return DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudMotivo as solm')
		->join('dnm_rrhh_si.Permisos.motivos as mot','solm.idMotivo','=','mot.idMotivo')
		->where('solm.idSolicitud',$idSolicitud)
		->where('mot.estado','A')
		->select('solm.idSolMot','solm.idSolicitud','mot.idMotivo','mot.nombre','solm.otro')->get();
		
		}
	}

}
