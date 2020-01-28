<?php namespace App\Models\dnm_catalogos;

use Illuminate\Database\Eloquent\Model;

class SysUsuarios extends Model {

	protected $table = 'dnm_catalogos.sys_usuarios';
    protected $primaryKey = 'idUsuario';
    const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';
    public $incrementing = false;       
    protected $connection = 'mysql';

}
