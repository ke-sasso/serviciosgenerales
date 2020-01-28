<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PlazasNominales extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.plazasNominales';
    protected $primaryKey = 'idPlazaNominal';
	protected $timestap = false;
	protected $connection = 'sqlsrv';


}
