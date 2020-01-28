<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class VwPermisos extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.vwPermisosRrhh';
    protected $primaryKey = 'id';
	public $timestamps = false;
	protected $connection = 'sqlsrv';

}
