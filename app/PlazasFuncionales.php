<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\rrhh\rh\Jefes;

class PlazasFuncionales extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.plazasFuncionales';
    protected $primaryKey = 'idPlazaFuncional';
	public $timestamps = false;
	protected $connection = 'sqlsrv';

	public  function jefes(){
    	return $this->hasMany('App\CatJefes','idPlazaFuncional','idPlazaFuncional');
    }

	public function unidad(){
		return $this->hasOne('App\Unidades','idUnidad','idUnidad');
	}

	public function esJefatura(){
		return Jefes::isThisPlazaABoss($this->idPlazaFuncional);
	}

	public function funciones()
	{
		return $this->hasMany('App\Models\rrhh\rh\Funciones','idPlazaFuncional','idPlazaFuncional');
	}


	public  function actitudes(){
    	return $this->hasMany('App\Models\rrhh\rh\ConocimientosPlazaFun','idPlazaFuncional','idPlazaFuncional');
    }
}
