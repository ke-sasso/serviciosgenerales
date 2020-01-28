<?php namespace App\Http\Controllers\EDC\AdminRH\Evaluaciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\rrhh\edc\Evaluaciones;
use App\Models\rrhh\edc\capacitacionesModel;
use App\Models\rrhh\edc\CapacitacionesDetalle;
use App\Models\rrhh\rh\evaluacionCapacitaItems;
use App\Models\rrhh\rh\DetCalificacionEmpleado;
use Illuminate\Http\Request;
use App\Http\Requests\capacitacionRequest;	
use Datatables;
use DB;
use Auth;
use Debugbar;
use Log;
use Session;

class capacitacionesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');

	}
	public function index()
	{
		$data = ['title' 			=> 'Plan de Capacitaciones'
				,'subtitle'			=> 'Análisis resultados EDC'
				,'breadcrumb' 		=> [
					['nom'	=>	'Plan de Capacitaciones', 'url' => '#'],
			 		['nom'	=>	'Análisis resultados EDC', 'url' => '#'],
				]]; 
		return view('edc.admin.capacitaciones.analisis.index',$data);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function crearNueva(capacitacionRequest $request)
	{
		DB::connection('sqlsrv')->beginTransaction();
		$capacitacion = null;
		try {
			if($request->idCapacitacion)
			{
				$capacitacion = capacitacionesModel::where('idCapacitacion',$request->idCapacitacion)->first();				
				$response = ['status' => 200, 'message' => 'Registro editado Exitosamente', "redirect" => ''];
			}
			else
			{
				$capacitacion = new capacitacionesModel();				
				$response = ['status' => 200, 'message' => 'Registro guardado Exitosamente', "redirect" => ''];
			}

			if($capacitacion)
			{
				$capacitacion->nombreCapacitacion = mb_strtoupper($request->nombreCapacitacion,'UTF-8');
				$capacitacion->fechaDesde = date('Y-m-d',strtotime($request->fechaDesde));
				$capacitacion->fechaHasta = date('Y-m-d',strtotime($request->fechaHasta));
				$capacitacion->idEvaluacion = $request->idEvaluacion;
				$capacitacion->idUsuarioCreacion = Auth::user()->idUsuario.'@'.$request->ip();
				$capacitacion->idUsuarioModificacion = Auth::user()->idUsuario.'@'.$request->ip();
				$capacitacion->save();	
				
				DB::connection('sqlsrv')->commit();	
			}
			else
			{
				$response = ['status' => 500, 'message' => 'No es posible realizar la acción solicitada', "redirect" => ''];				
			}
			
			

		} catch (\Exception $e) {
			Debugbar::addException($e);
			$response = ['status' => 500, 'message' => 'Se produjo una excepción en el servidor', "redirect" => ''];
			DB::connection('sqlsrv')->rollback();
		}
		
		return response()->json($response);
	}


	public function vwDetalleCapacitacion($id)
	{
		$cap = capacitacionesModel::where('idCapacitacion',$id)->get();
		$data = ['title' 			=> 'Plan de Capacitaciones'
				,'subtitle'			=> 'Administrador Capacitaciones'
				,'cap' => $cap
				,'breadcrumb' 		=> [
					['nom'	=>	'Administrador Capacitaciones', 'url' => route('rh.capacitaciones.admin')],
			 		['nom'	=>	$cap[0]->nombreCapacitacion, 'url' => '#']			 		
				],				
				];

		return view('edc.admin.capacitaciones.detalleCapacitacion',$data);	
	}
	public function listCapacitaciones()
	{
		$cap = DB::connection('sqlsrv')->table(DB::Raw('(Select ROW_NUMBER() OVER(ORDER BY b.nombre ASC) AS row, a.*,b.nombre from dnm_rrhh_si.RH.capacitaciones as a inner join dnm_rrhh_si.EDC.evaluaciones as b on a.idEvaluacion = b.idEvaluacion) as temp'))->select('temp.*');
		return Datatables::of($cap)->addColumn('editar',function($dt){
			return '<a href="'.url('training/vwDetalleCapacitacion').'/'.$dt->idCapacitacion.'" class="btn btn-sm btn-success"><li class="fa fa-folder-o fa-lg"></li></a>';
		})->make(true);
	}


	public function getDetalleCapacitacion($id)
	{
		$det = DB::connection('sqlsrv')->table(DB::Raw('(
			SELECT \'Desempeño\' AS tipo, vw.idEmpleado, vw.nombreEmpleado, vw.nombrePlaza, vw.nombreUnidad, vw.nombreEstado,
				CONCAT(\'Funcion: \',vw.nombreFuncion,\'<br><br>Tarea: \',vw.nombreTarea,\'<br><br>Desempeño: \',vw.nombreDesempenio) AS descripcion, vw.accionTomar
			FROM dnm_rrhh_si.RH.detalleCapacitaciones AS a 
			INNER JOIN dnm_rrhh_si.EDC.vwDesempenios AS vw ON a.idDesempenio = vw.idDesempenio 
			WHERE a.idCapacitacion = '.$id.' AND  a.idResultado = vw.idResultado

			UNION ALL

			SELECT \'Producto\' AS tipo, vw.idEmpleado, vw.nombreEmpleado, vw.nombrePlaza, vw.nombreUnidad, vw.nombreEstado,
				CONCAT(\'Funcion: \',vw.nombreFuncion,\'<br><br>Tarea: \',vw.nombreTarea,\'<br><br>Producto: \',vw.nombreProducto) AS descripcion, vw.accionTomar
			FROM dnm_rrhh_si.RH.detalleCapacitaciones AS a 
			INNER JOIN dnm_rrhh_si.EDC.vwProductos AS vw ON a.idProducto = vw.idProducto 
			WHERE a.idCapacitacion = '.$id.' AND  a.idResultado = vw.idResultado

			UNION ALL

			SELECT DISTINCT \'Conocimiento\' AS tipo, vw.idEmpleado, vw.nombreEmpleado, vw.nombrePlaza, vw.nombreUnidad, \'\' AS nombreEstado,
				vw.nombreTipoConocimiento AS descripcion, \'\' AS accionTomar
			FROM dnm_rrhh_si.RH.detalleCapacitaciones AS a 
			INNER JOIN dnm_rrhh_si.EDC.vwConocimientos AS vw ON a.idTipoConocimiento = vw.idTipoConocimiento 
			WHERE a.idCapacitacion = '.$id.' AND  a.idResultado = vw.idResultado

			UNION ALL

			Select 
				DISTINCT 
				\'Actitud\' as tipo,
				vw.idEmpleado,
				vw.nombreEmpleado, 
				vw.nombrePlaza,
				vw.nombreUnidad,
				\'\' AS nombreEstado,
				STUFF(
				 (
					SELECT DISTINCT \', \' +
					vw1.nombreTipoActitud
					FROM dnm_rrhh_si.EDC.vwActitudes AS vw1
					INNER JOIN dnm_rrhh_si.RH.detalleCapacitaciones cap1
					on cap1.idResultado = vw1.idResultado
					where vw1.idTipoActitud = cap1.idTipoActitud
					and cap1.idCapacitacion = '.$id.' and idEmpleado = vw.idEmpleado
					FOR XML PATH(\'\')),1,1,\'\'	 
				) as descripcion,
				\'\' AS accionTomar
			from
			dnm_rrhh_si.RH.detalleCapacitaciones cap
			inner join dnm_rrhh_si.EDC.vwActitudes vw on vw.idResultado = cap.idResultado
			where idCapacitacion = '.$id.' and vw.idTipoActitud = cap.idTipoActitud
		) AS temp'))->select('temp.*');



		return Datatables::of($det)->addColumn('add', function ($dt) {
        			return '';
	            })->make(true);		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show()
	{
		$data = ['title' 			=> 'Plan de Capacitaciones'
				,'subtitle'			=> 'Administrador Capacitaciones'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Index', 'url' => '#'],
				]];
		return view('edc.admin.capacitaciones.capacitacionesAdmin',$data);
	}	

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(capacitacionRequest $request)
	{
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getEvaluaciones()
	{
		$evals =  Evaluaciones::orderBy('fechaCreacion','desc')->get();

		return response()->json($evals);
	}

	public function showEmpCapacitaciones()
	{
		$data = ['title' 			=> 'Mis Capacitaciones'
				,'subtitle'			=> ''
				,'breadcrumb' 		=> [
			 		['nom'	=>	'', 'url' => '#'],
				]];
		return view('edc.empleado.capacitacionesEmp',$data);
	}

	public function getEvaluacionesEmp()
	{
		$evals = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.vwCapacitacionesEmpleado')->where('idEmpleado',Auth::user()->idEmpleado);		

		return Datatables::of($evals)->addColumn('editar',function($dt){
			if($dt->evaluar == 1 && !$dt->calificada)
			{
				return '<a href="'.url('training/evaluacionCapacitacion').'/'.$dt->idCapacitacion.'" class="btn btn-sm btn-success"><li class="fa fa-signal"></li> Encuesta</a>';	
			}
			else
			{
				return '';
			}
			
		})->make(true);
		
	}

	public function evaluacionCapacitacion($id)
	{	
		$data = ['title' 			=> 'Evaluacion Capacitacion'
				,'subtitle'			=> ''
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Mis Capacitaciones', 'url' => url('emp/training')],
			 		['nom'	=>	'Encuesta Capacitación', 'url' => '#']
				]];
		$capa = capacitacionesModel::findOrFail($id);
		$instituto = $capa->getInstitucionImparte($capa->entidad);
		$data['capacitacion']= $capa;
		$data['instituto'] = $instituto;
		$data['items']=evaluacionCapacitaItems::all();
		//dd($data);
		return view('edc.empleado.evaluacionCapacitacion',$data);
	}


	public function storeEvaluacionCapacitacion(Request $request){

		$opciones=$request->opcion;
		$eval = true;

		foreach ($opciones as $key => $value) {
			if($value == "")
			{
				$eval = false;
			}
		}

		if($eval)
		{
			DB::connection('sqlsrv')->beginTransaction();

			$detCalificacion = DetCalificacionEmpleado::where('idCapacitacion',$request->idCapacitacion)->where('idEmpleado',Auth::user()->idEmpleado);
			$detCalificacion->delete();


		    try {
				$opciones=$request->opcion;
				for($i=0;$i<count($opciones);$i++){
					$detCalificacion= new DetCalificacionEmpleado();

					$detCalificacion->idItem=$i+1;
					$detCalificacion->idCapacitacion=$request->idCapacitacion;
					$detCalificacion->calificacion=$opciones[$detCalificacion->idItem];
					$detCalificacion->idEmpleado=Auth::user()->idEmpleado;
					$detCalificacion->idUsuarioCreacion=Auth::user()->idUsuario;
					$detCalificacion->save();
				}			
			}
			catch(Exception $e){
			    DB::connection('sqlsrv')->rollback();
			    throw $e;
			    return $e;
			    Session::flash('msnError', $e->getMessage());
				return back();
			}
			DB::connection('sqlsrv')->commit();
			Session::flash('msnExito', 'LA ENCUESTA HA SIDO ENVIADA EXITOSAMENTE.');
			return redirect()->route('rh.capacitaciones.emp');
		}
		else
		{
			Session::flash('msnError', 'Debe completar cada uno de los items de la Encuesta');
			return redirect()->route('evaluacion.capacitaciones.rh',[$request->idCapacitacion]);	
		}
	}
}
