<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CatJefes extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.jefes';
    protected $primaryKey = 'idJefe';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';

	
}
