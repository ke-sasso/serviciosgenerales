<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;

class vwDesempenios extends Model {

	protected $table = 'dnm_rrhh_si.EDC.vwDesempenios';
	protected $primaryKey = 'idResultado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

}
