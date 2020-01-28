<?php namespace App\Http\Controllers\EDC;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Crypt;
use Validator;
use Mail;
use App\Unidades;
use App\PlazasFuncionales;
use App\CatEmpleados;

use App\Models\rrhh\rh\Jefes;
use App\Models\rrhh\rh\Empleados;
use App\Models\rrhh\rh\Funciones;
use App\Models\rrhh\rh\Tareas;
use App\Models\rrhh\rh\TareasActitudes;
use App\Models\rrhh\rh\TareasConocimientos;
use App\Models\rrhh\rh\TareasDesempenios;
use App\Models\rrhh\rh\TareasProductos;

use App\Models\rrhh\edc\Evaluaciones;

use App\Models\rrhh\edc\resultados\Resultados;
use App\Models\rrhh\edc\resultados\CompetenciasEstados;
use App\Models\rrhh\edc\resultados\Funciones as ResultadosFun;
use App\Models\rrhh\edc\resultados\Tareas as ResultadosTar;
use App\Models\rrhh\edc\resultados\TareasActitudes as ResultadosTarAct;
use App\Models\rrhh\edc\resultados\TareasConocimientos as ResultadosTarCon;
use App\Models\rrhh\edc\resultados\TareasDesempenios as ResultadosTarDes;
use App\Models\rrhh\edc\resultados\TareasProductos as ResultadosTarPro;

use App\Models\dnm_catalogos\SysUsuarios;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EvaluacionController extends Controller {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('auth');
	}

	public function editarTarea(Request $request, $idRes, $idTar){
		try{
			$eva = Evaluaciones::getEvaluacionVigente();
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			if(!(Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->esJefatura())){
				return view('errors.generic',['error' => 'Solo los usuarios con nivel de jefatura pueden realizar evaluaciones de desempeño!']);
			}

			$idResultado = Crypt::decrypt($idRes);
			$idTarea = Crypt::decrypt($idTar);

			$resultado = Resultados::findOrFail($idResultado);


			/*if($resultado->aprobada == 1){
				return view('errors.generic',['error' => 'Esta evaluación de desempeño ya fue finalizada, por lo que no se permiten realizar más cambios!']);
			}*/

			$reTar = ResultadosTar::where('idResultado',$idResultado)->where('idTarea',$idTarea)->first();
			if(empty($reTar)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño para los datos proporcionados!']);
			}

			if($resultado->idEvaluacion!=5){// 22 es el ID de Evaluacion Personal de Prueba
				$data = ['title' 			=> 'Evaluaciones desempeño'
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin')],
				 		['nom'	=>	'Evaluar', 'url' => route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion), 'idEmp' => Crypt::encrypt($resultado->idEmpleado)])],
				 		['nom'	=>	'Tarea', 'url' => '#'],
					]];
			}else{
				$data = ['title' 			=> 'Personal En Pruebas'
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Personal En Pruebas', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin.pruebas')],
				 		['nom'	=>	'Evaluar', 'url' => route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion), 'idEmp' => Crypt::encrypt($resultado->idEmpleado)])],
				 		['nom'	=>	'Tarea', 'url' => '#'],
					]];
			}


			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['resultado'] = $resultado;
			$data['reTar'] = $reTar;
			$data['estados'] = 	CompetenciasEstados::getDataEstados();

			$data['idEmp'] = Crypt::encrypt($resultado->idEmpleado);
			$data['idEva'] = Crypt::encrypt($resultado->idEvaluacion);


			return view('edc.evaluacion.tarea',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}

	public function updateTarea(Request $request){
		try {
			$rules = [
				'desempenios'			=>	'required|array|min:1',
				'estadoDesempenio'		=> 	'required|array|min:1',
				'txtAccionTomarDese'	=>	'required|array',
				'productos'				=>	'required|array|min:1',
				'estadoProducto'		=>	'required|array|min:1',
				'txtAccionTomarProd'	=>	'required|array',
				'conocimientos'			=>	'required|array|min:1',
				'estadoConocimiento'	=>	'required|array|min:1',
				'txtAccionTomarCono'	=>	'required|array',
				'actitudes'				=>	'required|array|min:1',
				'estadoActitud'			=>	'required|array|min:1',
				'txtAccionTomarActi'	=>	'required|array',
				'txtIdResultado'		=>	'required',
				'txtIdTarea'			=>	'required'
			];

			$v = Validator::make($request->all(),$rules);
			//Validaciones de sistema

			$v->setAttributeNames([
				'desempenios'			=>	'Desempeños',
				'estadoDesempenio'		=> 	'Estado desempeños',
				'txtAccionTomarDese'	=>	'required|array',
				'productos'				=>	'required|array|min:1',
				'estadoProducto'		=>	'required|array|min:1',
				'txtAccionTomarProd'	=>	'required|array',
				'conocimientos'			=>	'required|array|min:1',
				'estadoConocimiento'	=>	'required|array|min:1',
				'txtAccionTomarCono'	=>	'required|array',
				'actitudes'				=>	'required|array|min:1',
				'estadoActitud'			=>	'required|array|min:1',
				'txtAccionTomarActi'	=>	'required|array',
				'txtIdResultado'		=>	'required',
				'txtIdTarea'			=>	'required'
			]);

			$v->after(function ($v) use($request) {
				//Desempeños
				$cDese = true;
				foreach ($request->desempenios as $key => $value) {
					if($request->estadoDesempenio[$value] == "-1") $cDese = false;
				}
			    if (!$cDese) $v->errors()->add('estadoDesempenio', 'Debe evaluar todos los desempeños de la tarea!');
			    //Productos
				$cProd = true;
				foreach ($request->productos as $key => $value) {
					if($request->estadoProducto[$value] == "-1") $cProd = false;
				}
			    if (!$cProd) $v->errors()->add('estadoProducto', 'Debe evaluar todos los productos de la tarea!');
			    //Conocimientos
				$cCono = true;
				foreach ($request->conocimientos as $key => $value) {
					if($request->estadoConocimiento[$value] == "-1") $cCono = false;
				}
			    if (!$cCono) $v->errors()->add('estadoConocimiento', 'Debe evaluar todos los conocimientos de la tarea!');
			    //Actitudes
				$cActi = true;
				foreach ($request->actitudes as $key => $value) {
					if($request->estadoActitud[$value] == "-1") $cActi = false;
				}
			    if (!$cActi) $v->errors()->add('estadoActitud', 'Debe evaluar todas las actitudes de la tarea!');
			});

			if ($v->fails()){
				return redirect()->back()->withErrors($v)->withInput();
			}

			/*
			 *		ACTUALIZACIÓN DE DATOS TAREAS
			 */

			$id_resultado = Crypt::decrypt($request->txtIdResultado);
			$id_tarea = Crypt::decrypt($request->txtIdTarea);
			$usuario = Auth::user()->idUsuario.'@'.$request->ip();

			DB::connection('sqlsrv')->beginTransaction();

			$resultado = Resultados::findOrFail($id_resultado);
			$reTar = ResultadosTar::where('idResultado',$id_resultado)->where('idTarea',$id_tarea)->first();
			$reFun = ResultadosFun::where('idResultado',$id_resultado)->where('idFuncion',$reTar->idFuncion)->first();

			foreach ($request->actitudes as $acti) {
				ResultadosTarAct::where('idResultado',$id_resultado)->where('idActitud',$acti)->where('idTarea',$id_tarea)
				->update([
					'idEstado'			=> $request->estadoActitud[$acti],
					'accionTomar'		=> $request->txtAccionTomarActi[$acti],
					'idUsuarioModifica'	=> $usuario
				]);
			}

			foreach ($request->conocimientos as $cono) {
				ResultadosTarCon::where('idResultado',$id_resultado)->where('idConocimiento',$cono)->where('idTarea',$id_tarea)
				->update([
					'idEstado'			=> $request->estadoConocimiento[$cono],
					'accionTomar'		=> $request->txtAccionTomarCono[$cono],
					'idUsuarioModifica'	=> $usuario
				]);
			}

			foreach ($request->desempenios as $dese) {
				ResultadosTarDes::where('idResultado',$id_resultado)->where('idDesempenio',$dese)->where('idTarea',$id_tarea)
				->update([
					'idEstado'			=> $request->estadoDesempenio[$dese],
					'accionTomar'		=> $request->txtAccionTomarDese[$dese],
					'idUsuarioModifica'	=> $usuario
				]);
			}

			foreach ($request->productos as $prod) {
				ResultadosTarPro::where('idResultado',$id_resultado)->where('idProducto',$prod)->where('idTarea',$id_tarea)
				->update([
					'idEstado'			=> $request->estadoProducto[$prod],
					'accionTomar'		=> $request->txtAccionTomarProd[$prod],
					'idUsuarioModifica'	=> $usuario
				]);
			}

			$calValuesTar = $reTar->calcularCT($usuario);
			ResultadosTar::where('idResultado',$id_resultado)->where('idTarea',$id_tarea)->update($calValuesTar);

			$calValuesFun = $reFun->calcularCF($usuario);
			ResultadosFun::where('idResultado',$id_resultado)->where('idFuncion',$reTar->idFuncion)->update($calValuesFun);


			$resultado->calcularCP();
			$resultado->idUsuarioModifica = $usuario;
			$resultado->save();

			DB::connection('sqlsrv')->commit();
			return redirect()->route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion), 'idEmp' => Crypt::encrypt($resultado->idEmpleado)])->with('msnExito','Tarea actualizada de forma exitosa!');
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		} catch(Exception $e){
			DB::connection('sqlsrv')->rollback();
			throw $e;
			return $e->getMessage();
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

			if($resultado->idEvaluacion!=22){// 22 es el ID de Evaluacion Personal En Pruebas
				$data = ['title' 			=> 'Evaluaciones desempeño'
					,'subtitle'			=> 'Personal'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Personal', 'url' => route('edc.admin')],
				 		['nom'	=>	'Mostrar', 'url' => '#'],
					]];
			}else{
				$data = ['title' 			=> 'Personal En Pruebas'
					,'subtitle'			=> 'Personal'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Personal En Pruebas', 'url' => '#'],
				 		['nom'	=>	'Personal', 'url' => route('edc.admin')],
				 		['nom'	=>	'Mostrar', 'url' => '#'],
					]];
			}


			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['resultado'] = $resultado;
			$data['reTar'] = $reTar;
			$data['estados'] = 	CompetenciasEstados::getDataEstados();
			$data['is_historic'] = false;

			return view('edc.empleado.tareaShow',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}

	public function finalizar(Request $request,$idRes){
		try{
			$eva = Evaluaciones::getEvaluacionVigente();
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			$idResultado = Crypt::decrypt($idRes);

			$resultado = Resultados::findOrFail($idResultado);

            if($resultado->idEmpleado ==Auth::user()->idEmpleado ){
                return view('errors.generic',['error' => 'No puedes finalizar tu propia evaluación de desempeño!']);
            }

            if($resultado->idEvaluacion!=22){// 22 es el ID de Evaluacion Personal En Pruebas
            	$data = ['title' 			=> 'Evaluaciones desempeño'
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin')],
				 		['nom'	=>	'Evaluar', 'url' => route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion), 'idEmp' => Crypt::encrypt($resultado->idEmpleado)])],
				 		['nom'	=>	'Finalizar', 'url' => '#'],
					]];
            }else{
            	$data = ['title' 			=> 'Personal En Pruebas'
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Personal En Pruebas', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin.pruebas')],
				 		['nom'	=>	'Evaluar', 'url' => route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion), 'idEmp' => Crypt::encrypt($resultado->idEmpleado)])],
				 		['nom'	=>	'Finalizar', 'url' => '#'],
					]];
            }


			$data['emp'] = Empleados::findOrFail($resultado->idEmpleado);
			$data['resultado'] = $resultado;
			$data['estado'] = $resultado->cacularEstado();
			$data['idEmp'] = Crypt::encrypt($resultado->idEmpleado);
			$data['idEva'] = Crypt::encrypt($resultado->idEvaluacion);


			return view('edc.evaluacion.finalizar',$data);

		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}

	public function finalizarGuardar(Request $request){
		try {
			$rules = [
				'txtFechaEvaluacion'	=>	'required|date',
				'txtIdResultado'		=>	'required',
				'txtIdEstado'			=>	'required'
			];

			$v = Validator::make($request->all(),$rules);
			//Validaciones de sistema

			$v->setAttributeNames([
				'txtFechaEvaluacion'	=>	'Fecha evaluación',
				'txtIdResultado'		=>	'Id Resultado',
				'txtIdEstado'			=>	'Estado resolución'
			]);

			$v->after(function ($v) use($request) {
				$id_resultado = Crypt::decrypt($request->txtIdResultado);
				$resultado = Resultados::findOrFail($id_resultado);

				if($resultado->funciones()->where('finalizada',0)->count() <> 0){
					$v->errors()->add('funciones', 'Debe evaluar completamente todas las fuciones y tareas correspondientes para poder continuar!');
				}

				if($resultado->idEmpleado == Auth::user()->idEmpleado){
                    $v->errors()->add('autoevaluacion', 'No puedes finalizar tu propia evaluación de desempeño!');
                }

			});

			if ($v->fails()){
				return redirect()->back()->withErrors($v)->withInput();
			}

			/*
			 *		ACTUALIZACIÓN DE DATOS TAREAS
			 */

			$id_resultado = Crypt::decrypt($request->txtIdResultado);
			$id_estado = Crypt::decrypt($request->txtIdEstado);
			$usuario = Auth::user()->idUsuario.'@'.$request->ip();

			DB::connection('sqlsrv')->beginTransaction();

			$resultado = Resultados::findOrFail($id_resultado);
			if($resultado->finalizada==1) 	$resultado->aprobada = 0;
			$resultado->idEstado = $id_estado;
			$resultado->fechaEvaluacion = date('Y-m-d',strtotime($request->txtFechaEvaluacion));
			$resultado->finalizada = 1;
			$resultado->comentariosJefe=$request->comentarios;
			$resultado->compromisos = $request->compromisos;
			$resultado->idUsuarioModifica = $usuario;
			$resultado->save();

			DB::connection('sqlsrv')->commit();
			if($resultado->idEvaluacion!=5){
				//Enviamos correo menos a la EVALUACIONES DE PERSONAL EN PRUEBA
				//Consultamos al empleado que estan evaluando, para enviar correo
				$empleado=SysUsuarios::where('idEmpleado',$resultado->idEmpleado)->select('correo')->first();
				if(!empty($empleado->correo)){
								$correo=$empleado->correo;
								$data=[];
								Mail::send('emails.notificarEvalucionEmpleado',$data,function($msj) use ($data,$correo){
									$msj->from('solicitudes.administrativas@medicamentos.gob.sv','Evaluacion de Desempeño por Competencia');
									$msj->subject('Evaluación desempeño');
									$msj->to($correo);
								});
				}
			}

			return redirect()->route('edc.empleado.evaluar',['idEva' => Crypt::encrypt($resultado->idEvaluacion),'idEmp' => Crypt::encrypt($resultado->idEmpleado)])->with('msnExito', 'Evaluación de desempeño guardada de forma exitosa!');
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		} catch(Exception $e){
			DB::connection('sqlsrv')->rollback();
			throw $e;
			return $e->getMessage();
		}
	}

	public function aprobar(Request $request){
		try {
			$rules = [
				'txtIdResultado'			=>	'required'
			];

			$v = Validator::make($request->all(),$rules);
			//Validaciones de sistema

			$v->setAttributeNames([
				'txtIdResultado'		=>	'Id Resultado'
			]);

			if ($v->fails()){
				return redirect()->back()->withErrors($v)->withInput();
			}

			/*
			 *		ACTUALIZACIÓN DE RESULTADO
			 */

			$id_resultado = Crypt::decrypt($request->txtIdResultado);
			$usuario = Auth::user()->idUsuario.'@'.$request->ip();
			DB::connection('sqlsrv')->beginTransaction();
			$resultado = Resultados::findOrFail($id_resultado);
			$resultado->comentarios = $request->txtComentarios;
			$resultado->aprobada = 1;
			$resultado->idUsuarioModifica = $usuario;
			$resultado->save();
			DB::connection('sqlsrv')->commit();

			//Consultamos al jefe para enviar correo
			$idPlaza=PlazasFuncionales::find($resultado->idPlazaFuncional);
			$jefe=CatEmpleados::where('idPlazaFuncional',$idPlaza->idPlazaFuncionalPadre)->select('idEmpleado')->first();
			$empleado=SysUsuarios::where('idEmpleado',$jefe->idEmpleado)->select('correo')->first();
			if(!empty($empleado->correo)){
				$correo=$empleado->correo;
				$data['empleado']=SysUsuarios::where('idEmpleado',$resultado->idEmpleado)->select('nombresUsuario','apellidosUsuario')->first();
				if(strlen($request->txtComentarios)==0){$data['comentario']='SIN COMENTARIO';}else{$data['comentario']=$request->txtComentarios;}
				  Mail::send('emails.notificarEvaluacionJefe',$data,function($msj) use ($data,$correo){
				              $msj->from('solicitudes.administrativas@medicamentos.gob.sv','Evaluacion de Desempeño por Competencia');
		                      $msj->subject('Evaluación desempeño');
					          $msj->to($correo);
		           });
			 }


			return redirect()->route('edc.admin')->with('msnExito', 'Evaluación de desempeño guardada de forma exitosa!');
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		} catch(Exception $e){
			DB::connection('sqlsrv')->rollback();
			throw $e;
			return $e->getMessage();
		}
	}

	public function evaPersonalJefe(Request $request,$idRes){
		try{
			$eva = Evaluaciones::getEvaluacionVigente();
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			$idResultado = Crypt::decrypt($idRes);

			$resultado = Resultados::findOrFail($idResultado);

			$emp = Empleados::findOrFail($resultado->idEmpleado);
			$pf = $emp->plazaFuncional;
			$uni = Unidades::findOrFail($pf->idUnidad);

			$data = ['title' 			=> 'Evaluaciones desempeño'
					,'subtitle'			=> 'Personal'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Personal', 'url' => '#']
					]];

			$data['emp'] = $emp;
			$data['uni'] = $uni;
			$data['eva'] = $eva;


			$data['resultado'] = $resultado;
			$data['is_historic'] = false;

			return view('edc.empleado.personal',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}
}