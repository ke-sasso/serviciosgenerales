<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CatVehiculo extends Model {

	//
	protected $table = 'dnm_sol_admin_si.cat_vehiculos';
    protected $primaryKey = 'idVehiculo';
    protected $fillable=['idVehiculo','numPlaca','marca','modelo','tipo'];
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public static function getData($catvehiculos,$numplaca){

		foreach ($catvehiculos as $catvehiculo) {
			# code...
			if($catvehiculo->numPlaca == $numplaca){
				return $catvehiculo;
			}
		}

	}

	public static function getNumPlaca($idmotorista){

		return CatVehiculo::where('idMotorista',$idmoto)->lists('numPlaca','idVehiculo');

	}
	
}
