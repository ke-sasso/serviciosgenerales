<?php namespace App\Models\rrhh\rh;

use Illuminate\Database\Eloquent\Model;

use App\Models\rrhh\edc\resultados\Resultados;


class Empleados extends Model {

	protected $table = 'dnm_rrhh_si.RH.empleados';
    protected $primaryKey = 'idEmpleado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	public function plazaFuncional(){
		return $this->hasOne('App\PlazasFuncionales','idPlazaFuncional','idPlazaFuncional');
	}

	public function getNombreCompleto(){
		return trim($this->nombresEmpleado).' '.trim($this->apellidosEmpleado);
	}

	public function getTextoGenero(){
		switch ($this->sexo) {
			case 'M':
				return 'MASCULINO';
				break;
			case 'F':
				return 'FEMENINO';
				break;
			default:
				return '';
				break;
		}
	}

	public function getTextoNombrePlazaFuncional(){
		$pf = $this->plazaFuncional;
		if(empty($pf)){
			return '';
		}else{
			return $pf->nombrePlaza;
		}
	}

	public function getTextoNombreUnidad(){
		$pf = $this->plazaFuncional;
		if(empty($pf) && empty($pf->unidad)){
			return '';
		}else{
			return   mb_strtoupper($pf->unidad->nombreUnidad, 'UTF-8');
		}
	}

	public function getResultadoByIdEva($idEva){
		return Resultados::where('activo',1)->where('idEvaluacion',$idEva)->where('idEmpleado',$this->idEmpleado)->first();
	}
	
}
