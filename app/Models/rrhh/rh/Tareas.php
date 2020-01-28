<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class Tareas extends Model {

	protected $table = 'dnm_rrhh_si.RH.funcionesTareas';
    protected $primaryKey = 'idTarea';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function scopeActivas(){
		return $this->where('activo',1);
	}

	public function actitudes(){
		return $this->hasMany('App\Models\rrhh\rh\TareasActitudes','idTarea','idTarea');
	}

	public function conocimientos(){
		return $this->hasMany('App\Models\rrhh\rh\TareasConocimientos','idTarea','idTarea');
	}


	public function desempenios(){
		return $this->hasMany('App\Models\rrhh\rh\TareasDesempenios','idTarea','idTarea');
	}

	public function productos(){
		return $this->hasMany('App\Models\rrhh\rh\TareasProductos','idTarea','idTarea');
	}

	public function funcion(){
		return $this->hasOne('App\Models\rrhh\rh\Funciones','idFuncion','idFuncion');
	}

	public function conocimientosNivel(){
		return $this->hasMany('App\Models\rrhh\rh\TareasConocimientos','idTarea','idTarea')
			->join('dnm_rrhh_si.RH.conocimientoNiveles','conocimientoNiveles.idNivel','=','funcionesTareasConocimientos.idNivel');
	}
}