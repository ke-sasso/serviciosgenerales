<?php namespace App\Http\Controllers\EDC\AdminRH\Evaluaciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Crypt;
use Datatables;

use App\Models\rrhh\rh\Empleados;

use App\Unidades;
use App\PlazasFuncionales;
use App\PlazasNominales;

use App\Models\rrhh\edc\Evaluaciones;

use App\Models\rrhh\edc\resultados\Resultados;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;
use App\Models\rrhh\edc\resultados\Tareas as ResultadosTar;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FinalizadasController extends Controller {
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
					,'subtitle'			=> 'Administrador RH'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Finalizadas', 'url' => '#']
					]]; 


			$data['unidades']=Unidades::all();
			$data['plazasfun']=PlazasFuncionales::all();
			$data['plazasnom']=PlazasNominales::all();
			$data['evaluaciones']=Evaluaciones::all();
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
			
			return view('edc.admin.evaluacion.finalizadas',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}


	public function getDataRows(Request $request){

		$request->all();

		$emp = Empleados::findOrFail(Auth::user()->idEmpleado);
		$drs = DB::connection('sqlsrv')->table('dnm_rrhh_si.EDC.vwEvaluacionesHistorial');
		
        return Datatables::of($drs)
        	->addColumn('evaluacion', function ($dt) {
            	return '<a href="'.route('edc.rh.admin.mostrar',['idRes' => Crypt::encrypt($dt->idResultado)]).'" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>';
            })->removeColumn('idResultado')->removeColumn('idPlazaFuncionalPadre')
        	->filter(function($query) use ($request){
				if($request->has('idEvaluacion')){
					$query->where('idEvaluacion','=',(int)$request->get('idEvaluacion'));
				}
				if($request->has('empleado')){
	        		$query->where('empleado','like','%'.$request->get('empleado').'%');
	        	}
				if($request->has('unidad')){ 	
					$query->where('idUnidad','=',(int)$request->get('unidad'));
				}
				if($request->has('pfun')){
					$query->where('idPlazaFuncional','=',(int)$request->get('pfun'));
				}
				if($request->has('pnom')){
					$query->where('idPlazaNominal','=',(int)$request->get('pnom'));
				}
	        })
            ->make(true);
	}

	public function mostrar($idRes){
		try{
			$idResultado = Crypt::decrypt($idRes);
			$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Administrador RH'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Finalizadas', 'url' => route('edc.rh.admin')],
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
					,'subtitle'			=> 'Administrador RH'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Finalizadas', 'url' => route('edc.rh.admin')],
				 		['nom'	=>	'Mostrar', 'url' => route('edc.rh.admin.mostrar',['idRes' => $idRes])],
				 		['nom'	=>	'Tarea', 'url' => '#']
					]]; 
			
			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['resultado'] = $resultado;
			$data['reTar'] = $reTar;
			$data['estados'] = 	CompetenciasEstados::getDataEstados();
			$data['idRes'] = $idRes;
			$data['is_historic'] = false;

			return view('edc.empleado.tareaShow',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}
}