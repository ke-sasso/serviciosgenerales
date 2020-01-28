<?php namespace App\Models\dnm_ugda_si\CAT;

use Illuminate\Database\Eloquent\Model;

class tiposExpedientes extends Model {

	protected $table = 'dnm_ugda_si.CAT.tiposExpedientes';
	protected $primaryKey = 'idRegistroExpediente';
	protected $connection ='sqlsrv';
	public $incrementing = false;
	public $timestamps = false;


	public function unidad()
    {
        return $this->belongsTo('App\Models\rrhh\rh\unidades','idUnidad','idUnidad');
    }

}
