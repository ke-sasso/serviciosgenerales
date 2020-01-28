<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class TareasActitudes extends Model {

	protected $table = 'dnm_rrhh_si.RH.funcionesTareasActitudes';
    protected $primaryKey = 'idActitud';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function scopeActivas(){
		return $this->where('activo',1);
	}
}
