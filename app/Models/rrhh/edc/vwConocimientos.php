<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;

class vwConocimientos extends Model {

	protected $table = 'dnm_rrhh_si.EDC.vwConocimientos';
    protected $primaryKey = 'idResultado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
}
