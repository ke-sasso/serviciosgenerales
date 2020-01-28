<?php namespace App\Models\rrhh;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;
use DB;
class MarcasEmpleado extends Model {

	//
	protected $table = 'dnm_rrhh_si.RH.marcacion';
    protected $primaryKey = 'CodEmpleado';
	protected $timestap = false;
	protected $connection = 'sqlsrv';

	//$idEmpleado = null,$fechaInicio,$fechaFinal
	public static function getHistMarcacion()
	{
		//$fDesde = (string)date('Y-m-d',strtotime($fechaInicio));
		//$fHasta = date('Y-m-d',strtotime($fechaFinal.' +1 day'));
		//
		$marcacionesEmp = MarcasEmpleado::where('CodEmpleado','=',Auth::user()->idEmpleado)
									//->whereBetween(DB::raw('CONVERT (DATE, FechaMarca)'),array($fDesde,$fHasta))
									->orderBy('FechaMarca','DESC')
									->get()->toArray();
		//dd($marcacionesEmp);
		return $marcacionesEmp;
	}

	public static function getHistMarcacionByEmpleado($idEmpleado)
	{
		//$fDesde = (string)date('Y-m-d',strtotime($fechaInicio));
		//$fHasta = date('Y-m-d',strtotime($fechaFinal.' +1 day'));
		//
		$marcacionesEmp = MarcasEmpleado::where('CodEmpleado','=',$idEmpleado)
									->orderBy('FechaMarca','DESC')
									->get()->toArray();
		//dd($marcacionesEmp);
		return $marcacionesEmp;
	}
}
