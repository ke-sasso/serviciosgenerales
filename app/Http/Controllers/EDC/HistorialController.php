<?php namespace App\Http\Controllers\EDC;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Crypt;
use Datatables;

use App\Models\rrhh\rh\Empleados;

use App\Models\rrhh\edc\Evaluaciones;

use App\Models\rrhh\edc\resultados\Resultados;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;
use App\Models\rrhh\edc\resultados\Tareas as ResultadosTar;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HistorialController extends Controller {
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('auth');
	}

	public function index(){
		
		try{
			$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Historial'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Historial', 'url' => '#']
					]]; 
			/*
			$emp = Empleados::findOrFail(Auth::user()->idEmpleado);
			$pf = $emp->plazaFuncional;
			$uni = Unidades::findOrFail($pf->idUnidad);
						
			$data['emp'] = $emp;
			$data['uni'] = $uni;
			$data['eva'] = $eva;

			if($pf->esJefatura()){
				$filter = $uni->plazasFuncionales()->where('idPlazaFuncional','<>',$pf->idPlazaFuncional)->lists('idPlazaFuncional');
				$equipoTrabajo = Empleados::whereIn('idPlazaFuncional',$filter)->get();
				$data['equipoTrabajo'] = $equipoTrabajo;
				return view('edc.index',$data);
			}else{
				$data['subtitle'] = 'Personal';
				$data['breadcrumb'] = [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Personal', 'url' => '#']
					];

				$data['resultado'] = $emp->getResultadoByIdEva($eva->idEvaluacion);

				
			}*/
			return view('edc.historial',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}


	public function getDataRows(Request $request){
		$emp = Empleados::findOrFail(Auth::user()->idEmpleado);
		$drs = null;
		if($emp->plazaFuncional->esJefatura()){
			$drs = DB::connection('sqlsrv')->table('dnm_rrhh_si.EDC.vwEvaluacionesHistorial')->where('idPlazaFuncionalPadre',$emp->idPlazaFuncional)->where('activo',0);
		}else{
			$drs = DB::connection('sqlsrv')->table('dnm_rrhh_si.EDC.vwEvaluacionesHistorial')->where('idEmpleado',$emp->idEmpleado)->where('activo',0);
		}		
        return Datatables::of($drs)
        	->addColumn('evaluacion', function ($dt) {
            	return '<a href="'.route('edc.historial.mostrar',['idRes' => Crypt::encrypt($dt->idResultado)]).'" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>';
            })->removeColumn('idResultado')->removeColumn('idPlazaFuncionalPadre')->make(true);
	}

	public function mostrar($idRes){
		try{
			$idResultado = Crypt::decrypt($idRes);
			$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Historial'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Historial', 'url' => route('edc.historial')],
				 		['nom'	=>	'Mostrar', 'url' => '#']
					]]; 
			$resultado = Resultados::findOrFail($idResultado);
			$data['resultado'] = $resultado;
			$data['eva'] = Evaluaciones::findOrFail($resultado->idEvaluacion);
			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['is_historic'] = true;

			return view('edc.empleado.personal',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function mostrarTarea(Request $request, $idRes, $idTar){
		try{
			$idResultado = Crypt::decrypt($idRes);
			$idTarea = Crypt::decrypt($idTar);

			$resultado = Resultados::findOrFail($idResultado);
			$reTar = ResultadosTar::where('idResultado',$idResultado)->where('idTarea',$idTarea)->first();
			if(empty($reTar)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño para los datos proporcionados!']);
			}
			
			$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Historial'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Historial', 'url' => route('edc.historial')],
				 		['nom'	=>	'Mostrar', 'url' => route('edc.historial.mostrar',['idRes' => $idRes])],
				 		['nom'	=>	'Tarea', 'url' => '#']
					]]; 
			
			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['resultado'] = $resultado;
			$data['reTar'] = $reTar;
			$data['estados'] = 	CompetenciasEstados::getDataEstados();
			$data['idRes'] = $idRes;
			$data['is_historic'] = true;

			return view('edc.empleado.tareaShow',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}
}