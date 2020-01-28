<?php namespace App\Models\cssp;

use Illuminate\Database\Eloquent\Model;

class CsspProductos extends Model {

	protected $table = 'cssp.cssp_productos';
    protected $primaryKey = 'ID_PRODUCTO';
    const CREATED_AT = 'FECHA_CREACION';
	const UPDATED_AT = 'FECHA_MODIFICACION';
    public $incrementing = false;       
    protected $connection = 'mysql';

}
