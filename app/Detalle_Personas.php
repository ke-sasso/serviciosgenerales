<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Detalle_Personas extends Model {

	//
	protected $table = 'dnm_sol_admin_si.detalle_personas';
    protected $primaryKey = 'idDetalleP';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	 
}
