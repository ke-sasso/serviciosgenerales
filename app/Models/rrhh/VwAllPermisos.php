<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class VwAllPermisos extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.vwAllPermisos';
    protected $primaryKey = 'id';
	public $timestamps = false;
	protected $connection = 'sqlsrv';

}
