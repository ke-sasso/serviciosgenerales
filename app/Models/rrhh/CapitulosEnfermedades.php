<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class CapitulosEnfermedades extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.capituloEnfermedades';
    protected $primaryKey = 'idCapitulo';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
}
