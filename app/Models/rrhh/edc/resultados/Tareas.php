<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

class Tareas extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultadosFuncionesTareas';
    protected $primaryKey = ['idResultado','idTarea','idFuncion'];
	public $incrementing = false;
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public function funcion(){
		return $this->hasOne('App\Models\rrhh\rh\Funciones','idFuncion','idFuncion');
	}

	public function tarea(){
		return $this->hasOne('App\Models\rrhh\rh\Tareas','idTarea','idTarea');
	}

	public function desempenios(){
		$temp = $this->hasMany('App\Models\rrhh\edc\resultados\TareasDesempenios','idResultado','idResultado')
			->join('dnm_rrhh_si.RH.funcionesTareasDesempenios','funcionesTareasDesempenios.idDesempenio','=','resultadosFuncionesTareasDesempenios.idDesempenio');
		return $temp->where('funcionesTareasDesempenios.idTarea',$this->idTarea);
	}

	public function productos(){
		$temp = $this->hasMany('App\Models\rrhh\edc\resultados\TareasProductos','idResultado','idResultado')
			->join('dnm_rrhh_si.RH.funcionesTareasProductos','funcionesTareasProductos.idProducto','=','resultadosFuncionesTareasProductos.idProducto');
		return $temp->where('funcionesTareasProductos.idTarea',$this->idTarea);
	}

	public function conocimientos(){
		$temp = $this->hasMany('App\Models\rrhh\edc\resultados\TareasConocimientos','idResultado','idResultado')
			->join('dnm_rrhh_si.RH.funcionesTareasConocimientos','funcionesTareasConocimientos.idConocimiento','=','resultadosFuncionesTareasConocimientos.idConocimiento')
			->join('dnm_rrhh_si.RH.conocimientoNiveles','conocimientoNiveles.idNivel','=','funcionesTareasConocimientos.idNivel');
		return $temp->where('funcionesTareasConocimientos.idTarea',$this->idTarea);
	}

	public function actitudes(){
		$temp = $this->hasMany('App\Models\rrhh\edc\resultados\TareasActitudes','idResultado','idResultado')
			->join('dnm_rrhh_si.RH.funcionesTareasActitudes','funcionesTareasActitudes.idActitud','=','resultadosFuncionesTareasActitudes.idActitud');
		return $temp->where('funcionesTareasActitudes.idTarea',$this->idTarea);
	}

	public function calcularCT($usuario){
		$tareas = 0; $totales = 0; $parciales = 0; $minimas = 0; $CT = 0; $finalizada = 0;

		$tareas += $this->desempenios()->count();
		$tareas += $this->productos()->count();
		$tareas += $this->conocimientos()->count();
		$tareas += $this->actitudes()->count();
		$tareas += $tareas * 5; 

		$totales += $this->desempenios()->where('idEstado',1)->count();
		$totales += $this->productos()->where('idEstado',1)->count();
		$totales += $this->conocimientos()->where('idEstado',1)->count();
		$totales += $this->actitudes()->where('idEstado',1)->count();		
		$totales += $totales * 5;

		$parciales += $this->desempenios()->where('idEstado',2)->count();
		$parciales += $this->productos()->where('idEstado',2)->count();
		$parciales += $this->conocimientos()->where('idEstado',2)->count();
		$parciales += $this->actitudes()->where('idEstado',2)->count();		
		$parciales += $parciales * 3; 

		$minimas += $this->desempenios()->where('idEstado',3)->count();
		$minimas += $this->productos()->where('idEstado',3)->count();
		$minimas += $this->conocimientos()->where('idEstado',3)->count();
		$minimas += $this->actitudes()->where('idEstado',3)->count();	

		$CT = (($totales + $parciales + $minimas)/($tareas))*100;


		$finalizada += $this->desempenios()->whereNull('idEstado')->count();
		$finalizada += $this->productos()->whereNull('idEstado')->count();
		$finalizada += $this->conocimientos()->whereNull('idEstado')->count();
		$finalizada += $this->actitudes()->whereNull('idEstado')->count();

		$finalizada = ($finalizada>0)?0:1;

		return [
			'sumTareas' 		=> $tareas, 
			'sumTotales' 		=> $totales, 
			'sumParciales' 		=> $parciales, 
			'sumMinimas' 		=> $minimas, 
			'CT'				=> $CT,
			'finalizada'		=> $finalizada,
			'idUsuarioModifica'	=> $usuario
		];
	}
}
