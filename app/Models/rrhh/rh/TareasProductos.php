<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class TareasProductos extends Model {

	protected $table = 'dnm_rrhh_si.RH.funcionesTareasProductos';
    protected $primaryKey = 'idProducto';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function scopeActivos(){
		return $this->where('activo',1);
	}

}
