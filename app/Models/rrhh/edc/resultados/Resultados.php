<?php namespace App\Models\rrhh\edc\resultados;

use Illuminate\Database\Eloquent\Model;

use App\Models\rrhh\edc\resultados\Estados;

class Resultados extends Model {

	protected $table = 'dnm_rrhh_si.EDC.resultados';
    protected $primaryKey = 'idResultado';
	protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public function getDateFormat(){
		return 'Y-m-d H:i:s';
	}

	public function plazaFuncional(){
		return $this->hasOne('App\PlazasFuncionales','idPlazaFuncional','idPlazaFuncional');
	}

	public function funciones(){
		return $this->hasMany('App\Models\rrhh\edc\resultados\Funciones','idResultado','idResultado')->join('dnm_rrhh_si.RH.funciones','funciones.idFuncion','=','resultadosFunciones.idFuncion');
	}

	public function calcularCP(){
		$resultado = 0; $totales = 0; $parciales = 0; $minimas = 0; $CP = 0;

		$resultado = $this->funciones()->sum('sumFunciones');
		$totales = $this->funciones()->sum('sumTotales');
		$parciales = $this->funciones()->sum('sumParciales');
		$minimas = $this->funciones()->sum('sumMinimas');

		$CP = (($totales + $parciales + $minimas)/($resultado))*100;

		$this->sumResultado	= $resultado;
		$this->sumTotales = $totales;
		$this->sumParciales = $parciales;
		$this->sumMinimas = $minimas;
		$this->CP = $CP;
	}

	public function cacularEstado(){
		$CP = empty($this->CP)?0:$this->CP;

		return Estados::where('desde','<=',$CP)->where('hasta','>=',$CP)->first();
	}

	public function estado(){
		return $this->hasOne('App\Models\rrhh\edc\resultados\Estados','idEstado','idEstado');
	}
}
