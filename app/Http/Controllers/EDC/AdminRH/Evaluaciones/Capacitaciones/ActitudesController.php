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
use App\Models\rrhh\edc\vwActitudes;
use App\Models\rrhh\edc\capacitacionesModel;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;

use App\Models\rrhh\rh\ActitudesTipos;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ActitudesController extends Controller {
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
			 			['nom'	=>	'Actitudes', 'url' => '#'],
					]];
			$data['unidades']= Unidades::orderBy('nombreUnidad','asc')->get();
			$data['funcionales']= PlazasFuncionales::orderBy('nombrePlaza','asc')->get();
			$data['estados']= CompetenciasEstados::orderBy('nombreEstado','asc')->get();
			$data['evaluaciones'] = Evaluaciones::orderBy('fechaCreacion','desc')->get();

			$data['capacitaciones'] = capacitacionesModel::getCmbData();
			$data['tipo'] = 4; //Actitudes

			$data['categorias'] = ActitudesTipos::getCmbData();

			return view ('edc.admin.capacitaciones.analisis.actitudes',$data); 

		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function getDataRows(Request $request){
		if($request->searchCount > 0){
			$conocimientos=vwActitudes::where('idEstado',$request->estado)->select('idResultado','idEmpleado','nombreEmpleado', 'idPlazaFuncional','nombrePlaza','idUnidad','nombreUnidad','idTipoActitud','nombreTipoActitud','idEvaluacion','nombreEvaluacion','periodoEvaluacion')->distinct();

			return Datatables::of($conocimientos)
				->addColumn('add', function ($dt) {
        			return '<input type="checkbox" name="idResDet[\''.$dt->idResultado.'~'.$dt->idTipoActitud.'\']" value="'.$dt->idResultado.'~'.$dt->idTipoActitud.'">';
	            })->filter(function($query) use ($request){
					if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
					if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
					if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
					if($request->has('categoria'))		$query->whereIn('idTipoActitud',$request->categoria);
					

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
			$file = Excel::create('EDC_Actitudes', function($excel) use ($request) {
				 $excel->sheet('Actitudes', function($sheet) use ($request) {
				 	//$rowNumber = 1;
				 	$sheet->appendRow([
						    'nombreActitud','idEmpleado', 'nombreEmpleado','nombrePlaza','nombreUnidad','nombreEstado','nombreEvaluacion']);
				 	
				 	$estado = CompetenciasEstados::find($request->estado);
					$cuerpo = vwActitudes::where('idEstado',$request->estado)->where(function($query) use($request){
						if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
						if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
						if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
						if($request->has('categoria'))		$query->whereIn('idTipoActitud',$request->categoria);
					})->orderBy('idTipoActitud','asc')->orderBy('idUnidad','asc')->orderBy('idEmpleado','asc');
					
					foreach ($cuerpo->get() as $ddet) {
						//$rowNumber++;
						$sheet->appendRow([
						    $ddet->nombreTipoActitud,$ddet->idEmpleado, $ddet->nombreEmpleado,$ddet->nombrePlaza,$ddet->nombreUnidad,$estado->nombreEstado,$ddet->nombreEvaluacion
						]);
						
					}
					//$sheet->mergeCells('A'.$rowIni.':A'.$rowNumber);
				 	$sheet->setAutoFilter();
			    });
			})->string('xlsx');

			return response()->json([
					'name' => "EDC_Actitudes", //no extention needed
   					'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($file) //mime type of used format
				]);
		}
	}
}