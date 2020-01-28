<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_EstadoS extends Model {

	//
	protected $table = 'dnm_sol_admin_si.cat_estado_solicitud';
    protected $primaryKey = 'idEstado';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function trp_solicitud(){

    	return $this->hasMany('App\Trp_Solicitud');    
    }
}
