<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CatMotorista extends Model {

	//
	protected $table = 'dnm_sol_admin_si.cat_motorista';
    protected $primaryKey = 'idMotorista';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
}
