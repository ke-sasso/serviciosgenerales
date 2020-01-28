<?php namespace App\Models\rrhh\edc;

use Illuminate\Database\Eloquent\Model;

class vwProductos extends Model {

	protected $table = 'dnm_rrhh_si.EDC.vwProductos';
    protected $primaryKey = 'idResultado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
}
