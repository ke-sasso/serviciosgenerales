<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Trp_Solicitud extends Model {

	//
	protected $table = 'dnm_sol_admin_si.trp_solicitud';
    protected $primaryKey = 'idSolicitud';
    protected $fillable =['fechaTransporte','horaInicio','horaFin','lugar','descripcion','idEstado','idUsuarioCrea','fechaCreacion'];
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function cat_estadoS(){

    	return $this->belongsTo('App\Cat_EstadoS');    
    }

}
