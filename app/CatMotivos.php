<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CatMotivos extends Model {

	//
	
	protected $table = 'dnm_rrhh_si.Permisos.motivos';
    protected $primaryKey = 'idMotivo';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';

}
