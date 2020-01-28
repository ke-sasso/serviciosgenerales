<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;

class CatEnfermedades extends Model {

	protected $table = 'dnm_rrhh_si.Permisos.enfermedades';
    protected $primaryKey = 'idEnfermedad';
	protected $timestap = false;
	protected $connection = 'sqlsrv';
	
	public function scopeSearch($query,$nombreenfer){

    	return $query->where('nombreEnfermedad','LIKE',"%$nombreenfer%");
    }
	
}
//

