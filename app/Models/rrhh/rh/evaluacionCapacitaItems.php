<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

class evaluacionCapacitaItems extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.evaluacionCapacitacionItems';
    protected $primaryKey = 'idItem';
	protected $timestap = false;
	protected $connection = 'sqlsrv';


	protected function getDateFormat()
	{
		return 'Y-m-d';
	}
}
