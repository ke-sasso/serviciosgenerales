<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class CatDependientes extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.dependientes';
    protected $primaryKey = 'idDependiente';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
	
}
//dnm_rrhh_si.Permisos.enfermedades