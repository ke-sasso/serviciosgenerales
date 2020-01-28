<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;

class vwActitudes extends Model {

	protected $table = 'dnm_rrhh_si.EDC.vwActitudes';
    protected $primaryKey = 'idResultado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
}
