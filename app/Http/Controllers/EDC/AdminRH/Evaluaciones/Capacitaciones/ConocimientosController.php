<?php namespace App\Http\Controllers\EDC\AdminRH\Evaluaciones\Capacitaciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Excel;
use Crypt;
use Datatables;

use App\Unidades;
use App\PlazasFuncionales;

use App\Models\rrhh\edc\Evaluaciones;
use App\Models\rrhh\edc\vwConocimientos;
use App\Models\rrhh\edc\capacitacionesModel;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;

use App\Models\rrhh\rh\ConocimientosTipos;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConocimientosController extends Controller {
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
			$data = ['title' 			=> 'Plan de Capacitaciones'
					,'subtitle'			=> 'Análisis resultados EDC'
					,'breadcrumb' 		=> [
						['nom'	=>	'Plan de Capacitaciones', 'url' => '#'],
			 			['nom'	=>	'Análisis resultados EDC', 'url' => route('rh.capacitaciones.plan')],
			 			['nom'	=>	'Conocimientos', 'url' => '#'],
					]];
			$data['unidades']= Unidades::orderBy('nombreUnidad','asc')->get();
			$data['funcionales']= PlazasFuncionales::orderBy('nombrePlaza','asc')->get();
			$data['estados']= CompetenciasEstados::orderBy('nombreEstado','asc')->get();
			$data['evaluaciones'] = Evaluaciones::orderBy('fechaCreacion','desc')->get();

			$data['capacitaciones'] = capacitacionesModel::getCmbData();
			$data['tipo'] = 3; //Conocimientos

			$data['categorias'] = ConocimientosTipos::getCmbData();

			return view ('edc.admin.capacitaciones.analisis.conocimientos',$data); 

		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function getDataRows(Request $request){
		if($request->searchCount > 0){
			$conocimientos=vwConocimientos::where('idEstado',$request->estado)->select('idResultado','idEmpleado','nombreEmpleado', 'idPlazaFuncional','nombrePlaza','idUnidad','nombreUnidad','idTipoConocimiento','nombreTipoConocimiento','idEvaluacion','nombreEvaluacion','periodoEvaluacion')->distinct();

			return Datatables::of($conocimientos)
				->addColumn('add', function ($dt) {
        			return '<input type="checkbox" name="idResDet[\''.$dt->idResultado.'~'.$dt->idTipoConocimiento.'\']" value="'.$dt->idResultado.'~'.$dt->idTipoConocimiento.'">';
	            })->filter(function($query) use ($request){
					if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
					if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
					if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
					if($request->has('categoria'))		$query->whereIn('idTipoConocimiento',$request->categoria);
	        	})->removeColumn('idPlazaFuncional')->removeColumn('idEstado')->removeColumn('idEvaluacion')
	    		->make(true);
		}else{
	 		$results = ["draw" 			  => 0,		            
		        		"recordsTotal"    => 0,
		        		"recordsFiltered" => 0,
		          		"data"            => []];
			return json_encode($results);
	 	}
	}

	public function exportToExcel(Request $request){
		if($request->searchCount > 0){
			$file = Excel::create('EDC_Conocimientos', function($excel) use ($request) {
				 $excel->sheet('Conocimientos', function($sheet) use ($request) {
				 	//$rowNumber = 1;
				 	$sheet->appendRow([
						    'nombreConocimiento','idEmpleado', 'nombreEmpleado','nombrePlaza','nombreUnidad','nombreEstado','nombreEvaluacion']);
				 	
				 	$estado = CompetenciasEstados::find($request->estado);
					$cuerpo = vwConocimientos::where('idEstado',$request->estado)->where(function($query) use($request){
						if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
						if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
						if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
						if($request->has('categoria'))		$query->whereIn('idTipoConocimiento',$request->categoria);
					})->orderBy('idTipoConocimiento','asc')->orderBy('idUnidad','asc')->orderBy('idEmpleado','asc');
					
					foreach ($cuerpo->get() as $ddet) {
						//$rowNumber++;
						$sheet->appendRow([
						    $ddet->nombreTipoConocimiento,$ddet->idEmpleado, $ddet->nombreEmpleado,$ddet->nombrePlaza,$ddet->nombreUnidad,$estado->nombreEstado,$ddet->nombreEvaluacion
						]);
						
					}
					//$sheet->mergeCells('A'.$rowIni.':A'.$rowNumber);
				 	$sheet->setAutoFilter();
			    });
			})->string('xlsx');

			return response()->json([
					'name' => "EDC_Conocimientos", //no extention needed
   					'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($file) //mime type of used format
				]);
		}
	}
}