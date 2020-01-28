<?php namespace App\Models\dnm_ugda_si;

use Illuminate\Database\Eloquent\Model;

class PrestamoExpedientes extends Model {

	protected $table = 'dnm_ugda_si.PRES.prestamo_expedientes';
    protected $primaryKey = 'idPrestamo';    
    protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';


	public function bitacora(){

    	return $this->hasMany('App\Models\dnm_ugda_si\bitacoraDePrestamo','idPrestamo','idPrestamo');    
    }

    public function eSolicita(){
        return $this->hasOne('App\Models\rrhh\rh\Empleados','idEmpleado','idEmpleadoSolicitante');   
    }
}
