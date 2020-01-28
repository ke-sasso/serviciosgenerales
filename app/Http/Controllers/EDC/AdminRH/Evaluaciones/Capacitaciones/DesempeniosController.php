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
use App\Models\rrhh\edc\vwDesempenios;
use App\Models\rrhh\edc\capacitacionesModel;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DesempeniosController extends Controller {
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
			 			['nom'	=>	'Desempeños', 'url' => '#'],
					]];
			$data['unidades']= Unidades::orderBy('nombreUnidad','asc')->get();
			$data['funcionales']= PlazasFuncionales::orderBy('nombrePlaza','asc')->get();
			$data['estados']= CompetenciasEstados::orderBy('nombreEstado','asc')->get();
			$data['evaluaciones'] = Evaluaciones::orderBy('fechaCreacion','desc')->get();

			$data['capacitaciones'] = capacitacionesModel::getCmbData();
			$data['tipo'] = 1; //Desempeños

			return view ('edc.admin.capacitaciones.analisis.desempenios',$data); 

		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function getDataRows(Request $request){
		if($request->searchCount > 0){
			$desempenios=vwDesempenios::where('idEstado',$request->estado);

			return Datatables::of($desempenios)
				->addColumn('add', function ($dt) {
        			return '<input type="checkbox" name="idResDet[\''.$dt->idResultado.'~'.$dt->idDesempenio.'\']" value="'.$dt->idResultado.'~'.$dt->idDesempenio.'">';
	            })->filter(function($query) use ($request){
					if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
					if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
					if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
					

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
			$desempeniosCat =vwDesempenios::where('idEstado',$request->estado)->where(function($query) use($request){
				if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
				if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
				if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
			})->select('idDesempenio')->distinct();
					
			$file = Excel::create('EDC_Desempenios', function($excel) use ($desempeniosCat,$request) {
				 $excel->sheet('Desempeños', function($sheet) use ($desempeniosCat,$request) {
				 	//$rowNumber = 1;
				 	$sheet->appendRow([
						    'nombreDesempenio','idEmpleado', 'nombreEmpleado','nombrePlaza','nombreUnidad','nombreFuncion','nombreTarea','nombreEstado','accionTomar','nombreEvaluacion']);
				 	foreach ($desempeniosCat->get() as $dc) {
				 		//$rowIni = $rowNumber + 1;
				 		
						$cuerpo = vwDesempenios::where('idEstado',$request->estado)->where(function($query) use($request){
							if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
							if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
							if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
						})->where('idDesempenio',$dc->idDesempenio)->orderBy('idUnidad','asc')->orderBy('idEmpleado','asc');
						
						foreach ($cuerpo->get() as $ddet) {
							//$rowNumber++;
							$sheet->appendRow([
							    $ddet->nombreDesempenio,$ddet->idEmpleado, $ddet->nombreEmpleado,$ddet->nombrePlaza,$ddet->nombreUnidad,$ddet->nombreFuncion,$ddet->nombreTarea,$ddet->nombreEstado,$ddet->accionTomar,$ddet->nombreEvaluacion
							]);
							
						}
						//$sheet->mergeCells('A'.$rowIni.':A'.$rowNumber);
				 	}
				 	$sheet->setAutoFilter();
			    });
			})->string('xlsx');

			return response()->json([
					'name' => "EDC_Desempenios", //no extention needed
   					'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".base64_encode($file) //mime type of used format
				]);
		}
	}
}
/*

$cabe =vwDesempenios::where('idEstado',$request->estado)->where(function($query) use($request){
							if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
							if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
							if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
						})->where('idDesempenio',$dc->idDesempenio)->select('nombreDesempenio')->first();
				 		$sheet->appendRow([
						    $cabe->nombreDesempenio
						]);
						$sheet->mergeCells('A'.$rowNumber.':J'.$rowNumber);
						$rowNumber++;

						$cuerpo = vwDesempenios::where('idEstado',$request->estado)->where(function($query) use($request){
							if($request->has('unidad'))			$query->whereIn('idUnidad',$request->unidad);
							if($request->has('plazaFuncional'))	$query->whereIn('idPlazaFuncional',$request->plazaFuncional);
							if($request->evaluacion <> "-1")	$query->where('idEvaluacion',$request->evaluacion);
						})->where('idDesempenio',$dc->idDesempenio);
						$sheet->appendRow([
						    'idEmpleado', 'nombreEmpleado','nombrePlaza','nombreUnidad','nobreFuncion','nombreTarea','nombreDesempenio','nombreEstado','accionTomar','nombreEvaluacion']);
						$rowNumber++;
						foreach ($cuerpo->get() as $ddet) {
							$sheet->appendRow([
							    $ddet->idEmpleado, $ddet->nombreEmpleado,$ddet->nombrePlaza,$ddet->nombreUnidad,$ddet->nobreFuncion,$ddet->nombreTarea,$ddet->nombreDesempenio,$ddet->nombreEstado,$ddet->accionTomar,$ddet->nombreEvaluacion
							]);
							$rowNumber++;
						}
*/