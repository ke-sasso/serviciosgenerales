<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cssp_usuarios extends Model {

	protected $table = 'cssp.cssp_usuarios';
	protected $primaryKey = 'ID_USUARIO';
	CONST CREATED_AT = 'FECHA_CREACION';
	CONST UPDATED_AT = 'FECHA_MODIFICACION';
	protected $connection = 'transporte';
	

}
