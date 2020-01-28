<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class TareasConocimientos extends Model {

	protected $table = 'dnm_rrhh_si.RH.funcionesTareasConocimientos';
    protected $primaryKey = 'idConocimiento';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function scopeActivos(){
		return $this->where('activo',1);
	}
}
