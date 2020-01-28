<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class Funciones extends Model {

	protected $table = 'dnm_rrhh_si.RH.funciones';
    protected $primaryKey = 'idFuncion';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	protected $dateFormat = 'Y-m-d H:i:s';

	public function tareas(){
		return $this->hasMany('App\Models\rrhh\rh\Tareas','idFuncion','idFuncion');
	}

	public function scopeActivas(){
		return $this->where('activo',1);
	}
}