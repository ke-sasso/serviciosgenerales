<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVehiculo extends Model {

	//
	protected $table = 'dnm_sol_admin_si.detalle_vehiculo';
    protected $primaryKey = 'idDetalleV';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

}
