<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class TareasDesempenios extends Model {

	protected $table = 'dnm_rrhh_si.RH.funcionesTareasDesempenios';
    protected $primaryKey = 'idDesempenio';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function scopeActivos(){
		return $this->where('activo',1);
	}
}
