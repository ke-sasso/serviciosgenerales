<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class unidades extends Model {

	protected $table = 'dnm_rrhh_si.RH.unidades';
	protected $primaryKey = 'idUnidad';
	protected $connection = 'sqlsrv';
	public $timestamps = false;
	public $incrementing = false;

}
