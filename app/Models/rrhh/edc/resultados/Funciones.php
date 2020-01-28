<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

class Funciones extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultadosFunciones';
    protected $primaryKey = ['idResultado','idFuncion'];
	public $incrementing = false;
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public function tareas(){
		$temp = $this->hasMany('App\Models\rrhh\edc\resultados\Tareas','idResultado','idResultado')
			->join('dnm_rrhh_si.RH.funcionesTareas','funcionesTareas.idTarea','=','resultadosFuncionesTareas.idTarea');
		return $temp->where('funcionesTareas.idFuncion',$this->idFuncion);
	}

	public function calcularCF($usuario){
		$funciones = 0; $totales = 0; $parciales = 0; $minimas = 0; $CF = 0; $finalizada = 0;

		$funciones = $this->tareas()->sum('sumTareas');
		$totales = $this->tareas()->sum('sumTotales');
		$parciales = $this->tareas()->sum('sumParciales');
		$minimas = $this->tareas()->sum('sumMinimas');

		$CF = (($totales + $parciales + $minimas)/($funciones))*100;

		$finalizada = ($this->tareas()->count() == $this->tareas()->sum('finalizada'))?1:0;

		return [
			'sumFunciones' 		=> $funciones, 
			'sumTotales' 		=> $totales, 
			'sumParciales' 		=> $parciales, 
			'sumMinimas' 		=> $minimas, 
			'CF'				=> $CF,
			'finalizada'		=> $finalizada,
			'idUsuarioModifica'	=> $usuario
		];
	}
}
