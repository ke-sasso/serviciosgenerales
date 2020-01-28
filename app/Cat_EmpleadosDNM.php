<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Cat_EmpleadosDNM extends Model {

	//PRUEBA
	protected $table = 'dnm_catalogos.cat_empleados_dnm';
    protected $primaryKey = 'idEmpleado';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

}
