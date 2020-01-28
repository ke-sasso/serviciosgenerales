<?php namespace App\Models\cssp;

use Illuminate\Database\Eloquent\Model;

class siic_cosmeticos extends Model {

	protected $table = 'dnm_cosmeticos_si.CAT.vwRegistroscoshig';
	protected $primaryKey = 'noRegistro';
	public $timestamps = false;
	protected $connection = 'sqlsrv';

}
