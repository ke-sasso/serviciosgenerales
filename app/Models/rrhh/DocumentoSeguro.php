<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class DocumentoSeguro extends Model {

	//
	protected $table = 'dnm_rrhh_si.Permisos.documentosSeguro';
    protected $primaryKey = 'idDocumento';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
	protected $connection = 'sqlsrv';
}
