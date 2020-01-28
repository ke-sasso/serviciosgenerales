<?php namespace App\Http\Controllers\EDC;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Crypt;

use App\Unidades;
use App\PlazasFuncionales;
use App\PlazasNominales;

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

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Datatables;
use App\pdf\PdfEvaluacion;

class AdministradorController extends Controller {

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
			$eva = Evaluaciones::getEvaluacionVigente();
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => '#']
					]]; 

			$emp = Empleados::findOrFail(Auth::user()->idEmpleado);
			$pf = $emp->plazaFuncional;
			$uni = Unidades::findOrFail($pf->idUnidad);
						
			$data['emp'] = $emp;
			$data['uni'] = $uni;
			$data['eva'] = $eva;

			if($pf->esJefatura()){
				$filter = PlazasFuncionales::where('idPlazaFuncionalPadre',$pf->idPlazaFuncional)->lists('idPlazaFuncional');
				//$uni->plazasFuncionales()->where('idPlazaFuncional','<>',$pf->idPlazaFuncional)->lists('idPlazaFuncional');
				$equipoTrabajo = Empleados::whereIn('idPlazaFuncional',$filter)->where('contratoId',1)->where('estadoId',1);
				/*if($uni->tipoUnidad == 2){//Unidad Técnica
                    $equipoTrabajo->orWhere('idPlazaFuncional',$pf->idPlazaFuncional);
                }*/
				$data['equipoTrabajo'] = $equipoTrabajo->get();
				$data['resultado'] = $emp->getResultadoByIdEva($eva->idEvaluacion);

				return view('edc.index',$data);
			}else{
				$data['subtitle'] = 'Personal';
				$data['breadcrumb'] = [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Personal', 'url' => '#']
					];

				$data['resultado'] = $emp->getResultadoByIdEva($eva->idEvaluacion);
				$data['is_historic'] = false;

				return view('edc.empleado.personal',$data);
			}
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function indexPersonalEnPruebas(){
		try{// 5 es ID de Evaluacion de personal en pruebas
			$eva = Evaluaciones::where('idEvaluacion',5)->first();
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			$data = ['title' 			=> 'Personal En Pruebas' 
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Personal en pruebas', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => '#']
					]]; 

			$emp = Empleados::findOrFail(Auth::user()->idEmpleado);
			$pf = $emp->plazaFuncional;
			$uni = Unidades::findOrFail($pf->idUnidad);
						
			$data['emp'] = $emp;
			$data['uni'] = $uni;
			$data['eva'] = $eva;

			$filter = PlazasFuncionales::where('idPlazaFuncionalPadre',$pf->idPlazaFuncional)->lists('idPlazaFuncional');

			//contratoId = 4 es para Personal En Pruebas
            $empleados=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
                ->select(DB::raw('emp.idEmpleado,emp.nombresEmpleado, emp.apellidosEmpleado'))
                ->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
                ->where('fn.idPlazaFuncionalPadre',$pf->idPlazaFuncional)
                ->where('emp.estadoId',1)
                ->get();
			$equipoTrabajo = Empleados::whereIn('idPlazaFuncional',$filter)
                ->where('contratoId',4);
			$data['equipoTrabajo'] = $equipoTrabajo->get();
			$data['resultado'] = $emp->getResultadoByIdEva($eva->idEvaluacion);

			return view('edc.indexPersonalPruebas',$data);

		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}
	
	public function evaluar(Request $request,$idEva,$idEmp){
		try{
			if(!(Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->esJefatura())){
				return view('errors.generic',['error' => 'Solo los usuarios con nivel de jefatura pueden realizar evaluaciones de desempeño!']);	
			}

			$idEmpleado = Crypt::decrypt($idEmp);
			$idEvalucion = Crypt::decrypt($idEva);

			$eva = Evaluaciones::findOrFail($idEvalucion);
			if(empty($eva)){
				return view('errors.generic',['error' => 'No hay evaluaciones de desempeño vigentes!']);
			}

			if($eva->idEvaluacion!=22){// 22 es el ID para Evaluacion personal en pruebas
				$data = ['title' 			=> 'Evaluaciones desempeño' 
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Evaluaciones desempeño', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin')],
				 		['nom'	=>	'Evaluar', 'url' => '#']
					]];
			}else{
				$data = ['title' 			=> 'Personal En Pruebas' 
					,'subtitle'			=> 'Equipo trabajo'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Personal En Pruebas', 'url' => '#'],
				 		['nom'	=>	'Equipo trabajo', 'url' => route('edc.admin.pruebas')],
				 		['nom'	=>	'Evaluar', 'url' => '#']
					]];
			}
			 

			$emp = Empleados::findOrFail($idEmpleado);
			$resultado = AdministradorController::findOrCreateResultado($eva,$emp,Auth::user()->idUsuario.'@'.$request->ip());

			$data['emp'] = $emp;
			$data['eva'] = $eva;
			$data['resultado'] = $resultado;

			return view('edc.evaluacion.admin',$data);
			
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}

	public function vistaPrevia(Request $request,$idEva,$idEmp){

		$pdf = new PdfEvaluacion();
		$pdf->SetAuthor('Dirección Nacional de Medicamentos');
		$pdf->SetTitle('Evaluacion de Desempeño');
		$pdf->SetSubject('Evaluacion');
		$pdf->SetKeywords('DNM, PDF, Evaluacion');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		$pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setFontSubsetting(true);

		$pdf->AddPage('P');

		$emp = Empleados::findOrFail($idEmp);
		$eva = Evaluaciones::findOrFail($idEva);
		/*$resultado = AdministradorController::findOrCreateResultado($eva,$emp,Auth::user()->idUsuario.'@'.$request->ip());*/
		$jefe = plazasFuncionales::findOrFail($emp->plazaFuncional->idPlazaFuncionalPadre);
		//$estados = CompetenciasEstados::getDataEstados();

		$pdf->SetFont('Times','',8);

		if($emp->plazaFuncional->unidad->tipoUnidad==1){
			$area = 'Administrativa';
		}else{
			$area = 'Técnica';
		}

		$resultado = Resultados::where('idEvaluacion',$idEva)->where('idEmpleado',$idEmp)
		->where('idPlazaFuncional',$emp->plazaFuncional->idPlazaFuncional)->first();

		$tabla = '<table border="1">
				<tbody>
				<tr align="left" >
					<td  style="width: 535px"><strong> Nombre: </strong>'.$emp->nombresEmpleado.' '.$emp->apellidosEmpleado.'</td>
					<td  style="width: 110px"><strong> Codigo: </strong>'.$emp->idEmpleado.'</td>		
				</tr>
				<tr align="left" >
					<td  style="width: 425px"><strong> Nombre del Puesto: </strong>'.$emp->getTextoNombrePlazaFuncional().'</td>
					<td  style="width: 110px"><strong> Área: </strong>'.$area.'</td>
					<td  style="width: 110px"><strong> Salario: </strong>$</td>		
				</tr>
				<tr align="left" >
					<td  style="width: 645px"><strong> Subordinación: </strong>'.$jefe->nombrePlaza.'</td>
				</tr>
				<tr align="left" >
					<td  style="width: 210px"><strong> Fecha Ingreso: </strong></td>
					<td  style="width: 225px"><strong> Período a Evaluar: </strong>'.$eva->periodo.'</td>
					<td  style="width: 210px"><strong> Fecha de Evaluacion: </strong>'.$resultado->fechaEvaluacion.' </td>
				
				</tr>
				<tr align="left">
					<td style="width: 645px"></td>
				</tr>
				
			</tbody></table>';
		$pdf->writeHTML($tabla, true, false, true, false, '');

		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetXY($x, $y);		

		$tabla = '<table border="1">
				<tbody>';

		foreach($resultado->funciones()->orderBy('literal','asc')->get() as $f){
			$tareas = $f->tareas()->orderBy('numero','asc')->get();
			for ($i=0; $i < count($tareas) ; $i++) { 
				$tabla.= '<tr nobr="true">
					<td style="width: 350px"><strong>FUNCIÓN: </strong>'.$f->literal.'. '.$f->nombreFuncion.'</td>
					<td style="width: 295px"><strong>TAREA </strong>['.$tareas[$i]->numero.' de '.count($tareas).']: '.$f->literal.'.'.$tareas[$i]->numero.' '.$tareas[$i]->nombreTarea.'</td>
					</tr>
					<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Desempeño</strong></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Criterios de desempeño</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

				$reTar = ResultadosTar::where('idResultado',$resultado->idResultado)->where('idTarea',$tareas[$i]->idTarea)->first();

				foreach ($reTar->desempenios()->orderBy('numero','asc')->get() as $dese) {

					if($dese->idEstado!=null ||$dese->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$dese->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }

					$tabla.= '
					<tr nobr="true">
							<td style="width: 400px"> '.$dese->numero.' '.$dese->nombreDesempenio.'</td>
							<td style="width: 120px;text-align:center">'.$estado.'</td>
							<td style="width: 125px">'.$dese->accionTomar.'</td>
					</tr>';
				}

			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Productos</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Productos</strong></td>
						<td style="width: 120px;height:35px;"><strong>Verificacion de evidencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';
				foreach ($reTar->productos()->orderBy('numero','asc')->get() as $prod) {

					if($prod->idEstado!=null ||$prod->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$prod->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }
					$tabla.= '
					<tr nobr="true">
							<td style="width: 400px"> '.$prod->numero.' '.$prod->nombreProducto.'</td>
							<td style="width: 120px;text-align:center">'.$estado.'</td>
							<td style="width: 125px">'.$prod->accionTomar.'</td>
					</tr>';
				}
			
			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Conocimientos</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 300px;height:35px;"><strong>Conocimientos</strong></td>
						<td style="width: 100px;height:35px;"><strong>Nivel</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

			foreach ($reTar->conocimientos()->orderBy('numero','asc')->get() as $cono) {

				if($cono->idEstado!=null ||$cono->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$cono->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }
					$tabla.= '
					<tr nobr="true">
							<td style="width: 300px"> '.$cono->numero.' '.$cono->nombreConocimiento.'</td>
							<td style="width: 100px"> '.$cono->nombreNivel.'</td>
							<td style="width: 120px;text-align:center"> '.$estado.'</td>
							<td style="width: 125px"> '.$cono->accionTomar.'</td>
					</tr>';
			}

			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Actitudes</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Actitudes</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

			foreach ($reTar->actitudes()->orderBy('numero','asc')->get() as $acti) {
				
				if($acti->idEstado!=null ||$acti->idEstado!=''){
					$estado = CompetenciasEstados::where('idEstado',$acti->idEstado)->select('nombreEstado')->first();
					$estado = $estado->nombreEstado;
				}else{$estado = ''; }

				$tabla.= '
				<tr nobr="true">
						<td style="width: 400px"> '.$acti->numero.' '.$acti->nombreActitud.'</td>
						<td style="width: 120px;text-align:center">'.$estado.'</td>
						<td style="width: 125px">'.$acti->accionTomar.'</td>
				</tr>';
			}

			$tabla .='<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="center" nobr="true">
						<td style="width: 645px;" height="35px"><strong>RESULTADO CT: '.$tareas[$i]->CT.'%'.'</strong></td>
					</tr>
					<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>';

			}			
			
		}

		$cp = (!empty($resultado->CP)) ? $resultado->CP : '' ;
		$resultado = (!empty($resultado->estado->nombreEstado)) ? $resultado->estado->nombreEstado: '';
		$tabla .='<tr align="center" nobr="true">
					<td style="width: 645px;" height="35px"><strong>RESUMEN DE RESULTADO OBTENIDO<br>CP: '.$cp.'%&nbsp;&nbsp;&nbsp;&nbsp;RESULTADO: '.$resultado.'</strong></td>
				</tr>
				</tbody></table>';

		$pdf->writeHTML($tabla, true, false, true, false, '');

		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetXY($x, $y);

		$tabla = '<table border="1">
				<tbody>
					<tr nobr="true">
						<td style="width: 450px;height:75px;"><strong>Nombre del jefe que evalúa:</strong></td>
						<td style="width: 195px;height:75px;" align="center"><strong>Firma y sello de quien evalúa</strong></td>
					</tr>
					<tr nobr="true">
						<td style="width: 450px;height:100px;"><strong>Comentarios de la persona evaluada:</strong></td>
						<td style="width: 195px;height:100px;" align="center"><strong>Firma de la persona evaluada</strong></td>
					</tr>
				</tbody></table>';

		$pdf->writeHTML($tabla, true, false, true, false, '');

		$pdf->Output('Evaluacion.pdf');
		//return "llega al controlador";

	}
	public function formatoEvaluacion(Request $request,$idEva,$idEmp){

		$pdf = new PdfEvaluacion();
		$pdf->SetAuthor('Dirección Nacional de Medicamentos');
		$pdf->SetTitle('Evaluacion de Desempeño');
		$pdf->SetSubject('Evaluacion');
		$pdf->SetKeywords('DNM, PDF, Evaluacion');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		$pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
		
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setFontSubsetting(true);

		$pdf->AddPage('P');

		$emp = Empleados::findOrFail($idEmp);
		$eva = Evaluaciones::findOrFail($idEva);
		/*$resultado = AdministradorController::findOrCreateResultado($eva,$emp,Auth::user()->idUsuario.'@'.$request->ip());*/
		$jefe = plazasFuncionales::findOrFail($emp->plazaFuncional->idPlazaFuncionalPadre);
		//$estados = CompetenciasEstados::getDataEstados();

		$pdf->SetFont('Times','',8);

		if($emp->plazaFuncional->unidad->tipoUnidad==1){
			$area = 'Administrativa';
		}else{
			$area = 'Técnica';
		}

		$resultado = Resultados::where('idEvaluacion',$idEva)->where('idEmpleado',$idEmp)
		->where('idPlazaFuncional',$emp->plazaFuncional->idPlazaFuncional)->first();

		$tabla = '<table border="1">
				<tbody>
				<tr align="left" >
					<td  style="width: 535px"><strong> Nombre: </strong>'.$emp->nombresEmpleado.' '.$emp->apellidosEmpleado.'</td>
					<td  style="width: 110px"><strong> Codigo: </strong>'.$emp->idEmpleado.'</td>		
				</tr>
				<tr align="left" >
					<td  style="width: 425px"><strong> Nombre del Puesto: </strong>'.$emp->getTextoNombrePlazaFuncional().'</td>
					<td  style="width: 110px"><strong> Área: </strong>'.$area.'</td>
					<td  style="width: 110px"><strong> Salario: </strong>$</td>		
				</tr>
				<tr align="left" >
					<td  style="width: 645px"><strong> Subordinación: </strong>'.$jefe->nombrePlaza.'</td>
				</tr>
				<tr align="left" >
					<td  style="width: 210px"><strong> Fecha Ingreso: </strong></td>
					<td  style="width: 225px"><strong> Período a Evaluar: </strong>'.$eva->periodo.'</td>
					<td  style="width: 210px"><strong> Fecha de Evaluacion: </strong>'.$resultado->fechaEvaluacion.' </td>
				
				</tr>
				<tr align="left">
					<td style="width: 645px"></td>
				</tr>
				
			</tbody></table>';
		$pdf->writeHTML($tabla, true, false, true, false, '');

		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetXY($x, $y);		

		$tabla = '<table border="1">
				<tbody>';

		foreach($resultado->funciones()->orderBy('literal','asc')->get() as $f){
			$tareas = $f->tareas()->orderBy('numero','asc')->get();
			for ($i=0; $i < count($tareas) ; $i++) { 
				$tabla.= '<tr nobr="true">
					<td style="width: 350px"><strong>FUNCIÓN: </strong>'.$f->literal.'. '.$f->nombreFuncion.'</td>
					<td style="width: 295px"><strong>TAREA </strong>['.$tareas[$i]->numero.' de '.count($tareas).']: '.$f->literal.'.'.$tareas[$i]->numero.' '.$tareas[$i]->nombreTarea.'</td>
					</tr>
					<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Desempeño</strong></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Criterios de desempeño</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

				$reTar = ResultadosTar::where('idResultado',$resultado->idResultado)->where('idTarea',$tareas[$i]->idTarea)->first();

				foreach ($reTar->desempenios()->orderBy('numero','asc')->get() as $dese) {

					if($dese->idEstado!=null ||$dese->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$dese->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }

					$tabla.= '
					<tr nobr="true">
							<td style="width: 400px"> '.$dese->numero.' '.$dese->nombreDesempenio.'</td>
							<td style="width: 120px;text-align:center"></td>
							<td style="width: 125px"></td>
					</tr>';
				}

			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Productos</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Productos</strong></td>
						<td style="width: 120px;height:35px;"><strong>Verificacion de evidencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';
				foreach ($reTar->productos()->orderBy('numero','asc')->get() as $prod) {

					if($prod->idEstado!=null ||$prod->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$prod->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }
					$tabla.= '
					<tr nobr="true">
							<td style="width: 400px"> '.$prod->numero.' '.$prod->nombreProducto.'</td>
							<td style="width: 120px;text-align:center"></td>
							<td style="width: 125px"></td>
					</tr>';
				}
			
			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Conocimientos</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 300px;height:35px;"><strong>Conocimientos</strong></td>
						<td style="width: 100px;height:35px;"><strong>Nivel</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

			foreach ($reTar->conocimientos()->orderBy('numero','asc')->get() as $cono) {

				if($cono->idEstado!=null ||$cono->idEstado!=''){
						$estado = CompetenciasEstados::where('idEstado',$cono->idEstado)->select('nombreEstado')->first();
						$estado = $estado->nombreEstado;
					}else{$estado = ''; }
					$tabla.= '
					<tr nobr="true">
							<td style="width: 300px"> '.$cono->numero.' '.$cono->nombreConocimiento.'</td>
							<td style="width: 100px"> '.$cono->nombreNivel.'</td>
							<td style="width: 120px;text-align:center"> </td>
							<td style="width: 125px"></td>
					</tr>';
			}

			$tabla .= '<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="Center" nobr="true">
						<td style="width: 645px"><strong>Actitudes</strong></td>
						</tr>
					<tr align="Center" nobr="true">
						<td style="width: 400px;height:35px;"><strong>Actitudes</strong></td>
						<td style="width: 120px;height:35px;"><strong>Nivel de Competencia</strong></td>
						<td style="width: 125px;height:35px;"><strong>Accion a tomar</strong></td>
					</tr>';

			foreach ($reTar->actitudes()->orderBy('numero','asc')->get() as $acti) {
				
				if($acti->idEstado!=null ||$acti->idEstado!=''){
					$estado = CompetenciasEstados::where('idEstado',$acti->idEstado)->select('nombreEstado')->first();
					$estado = $estado->nombreEstado;
				}else{$estado = ''; }

				$tabla.= '
				<tr nobr="true">
						<td style="width: 400px"> '.$acti->numero.' '.$acti->nombreActitud.'</td>
						<td style="width: 120px;text-align:center"></td>
						<td style="width: 125px"></td>
				</tr>';
			}

			$tabla .='<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>
					<tr align="center" nobr="true">
						<td style="width: 645px;" height="35px"><strong>RESULTADO CT: </strong></td>
					</tr>
					<tr align="left" nobr="true">
						<td style="width: 645px"></td>
					</tr>';

			}			
			
		}

		$tabla .='<tr align="center" nobr="true">
					<td style="width: 645px;" height="35px"><strong>RESUMEN DE RESULTADO OBTENIDO<br>CP:%&nbsp;&nbsp;&nbsp;&nbsp;RESULTADO: </strong></td>
				</tr>
				</tbody></table>';

		$pdf->writeHTML($tabla, true, false, true, false, '');

		$x = $pdf->GetX();
		$y = $pdf->GetY();

		$pdf->SetXY($x, $y);

		$tabla = '<table border="1">
				<tbody>
					<tr nobr="true">
						<td style="width: 450px;height:75px;"><strong>Nombre del jefe que evalúa:</strong></td>
						<td style="width: 195px;height:75px;" align="center"><strong>Firma y sello de quien evalúa</strong></td>
					</tr>
					<tr nobr="true">
						<td style="width: 450px;height:100px;"><strong>Comentarios de la persona evaluada:</strong></td>
						<td style="width: 195px;height:100px;" align="center"><strong>Firma de la persona evaluada</strong></td>
					</tr>
				</tbody></table>';

		$pdf->writeHTML($tabla, true, false, true, false, '');

		$pdf->Output('Evaluacion.pdf');
		//return "llega al controlador";

	}

	private static function findOrCreateResultado($eva,$emp,$usuario){
		$resultado = Resultados::where('idEvaluacion',$eva->idEvaluacion)->where('idEmpleado',$emp->idEmpleado)->where('idPlazaFuncional',$emp->idPlazaFuncional)->first();
		if(empty($resultado)){
			/*
			 *		NUEVA EVALUACION INSPECCIÓN BUENAS PRÁCTICAS
			 */
			DB::connection('sqlsrv')->beginTransaction();
			try {
 
				$resultado = New Resultados;
				$resultado->idEvaluacion = $eva->idEvaluacion;
				$resultado->idEmpleado = $emp->idEmpleado;
				$resultado->idPlazaFuncional = $emp->idPlazaFuncional;
				$resultado->idUsuarioCrea = $usuario;
				$resultado->save(); 

				$idNewResult = $resultado->idResultado;

				/*
				 *		DETALLE FUNCIONES
				 */
				$funciones = Funciones::activas()->where('idPlazaFuncional',$emp->idPlazaFuncional)->lists('idFuncion');
				foreach ($funciones as $fun) {
					$reFun = New ResultadosFun;
					$reFun->idResultado = $idNewResult;
					$reFun->idFuncion = $fun;
					$reFun->idUsuarioCrea = $usuario;
					$reFun->save();


					/*
					 *		DETALLE TAREAS FUNCIONES
					 */
					$tareas = Tareas::activas()->where('idFuncion',$fun)->lists('idTarea');
					foreach ($tareas as $tar) {
						$reTar = New ResultadosTar;
						$reTar->idResultado = $idNewResult;
						$reTar->idTarea = $tar;
						$reTar->idFuncion = $fun;
						$reTar->idUsuarioCrea = $usuario;
						$reTar->save();

						/*
						 *		DETALLE ACTITUDES TAREA
						 */
						$actitudes = TareasActitudes::activas()->where('idTarea',$tar)->lists('idActitud');
						foreach ($actitudes as $acti) {
							$tarAc = New ResultadosTarAct;
							$tarAc->idResultado = $idNewResult;
							$tarAc->idActitud = $acti;
							$tarAc->idTarea = $tar;
							$tarAc->idUsuarioCrea = $usuario;
							$tarAc->save();
						}

						/*
						 *		DETALLE CONOCIMIENTOS TAREA
						 */
						$conocimientos = TareasConocimientos::activos()->where('idTarea',$tar)->lists('idConocimiento');
						foreach ($conocimientos as $cono) {
							$tarCo = New ResultadosTarCon;
							$tarCo->idResultado = $idNewResult;
							$tarCo->idConocimiento = $cono;
							$tarCo->idTarea = $tar;
							$tarCo->idUsuarioCrea = $usuario;
							$tarCo->save();
						}

						/*
						 *		DETALLE DESEMPEÑOS TAREA
						 */
						$desempenio = TareasDesempenios::activos()->where('idTarea',$tar)->lists('idDesempenio');
						foreach ($desempenio as $dese) {
							$tarCo = New ResultadosTarDes;
							$tarCo->idResultado = $idNewResult;
							$tarCo->idDesempenio = $dese;
							$tarCo->idTarea = $tar;
							$tarCo->idUsuarioCrea = $usuario;
							$tarCo->save();
						}

						/*
						 *		DETALLE DESEMPEÑOS TAREA
						 */
						$productos = TareasProductos::activos()->where('idTarea',$tar)->lists('idProducto');
						foreach ($productos as $prod) {
							$tarCo = New ResultadosTarPro;
							$tarCo->idResultado = $idNewResult;
							$tarCo->idProducto = $prod;
							$tarCo->idTarea = $tar;
							$tarCo->idUsuarioCrea = $usuario;
							$tarCo->save();
						}
					}
				}

				

			} catch(Exception $e){
				DB::connection('sqlsrv')->rollback();
				throw $e;
				return response()->json(['status' => 400, 'message' => $e->getMessage()]);
			}  
			DB::connection('sqlsrv')->commit();
		}
		return $resultado;
	}


	public function showPerfilesPuesto(){	
		$data = ['title' 			=> 'Perfiles de Puesto' 
				,'subtitle'			=> 'Administrador RH'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Perfiles Puesto', 'url' => '#']
				]]; 
		
		$data['unidades']=Unidades::all();
		$data['plazasfun']=PlazasFuncionales::all();
		$data['plazasnom']=PlazasNominales::all();

		return view('pp.perfilespuesto',$data);
	}

	public function getDataRowsPerfilesP(Request $request){
		
		$drs = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.vwEmpleados');
				
        return Datatables::of($drs)
        	->addColumn('mostrar', function ($dt) {
            	return '<a href="'.route('perfiles.puesto.emp',['idEmp' => Crypt::encrypt($dt->idEmpleado)]).'" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-eye"></i> Mostrar</a>';
            })

        	->filter(function($query) use ($request){
							
	        				if($request->has('empleado')){
	        					$query->where('empleado','like','%'.$request->get('empleado').'%');
	        				}

	        				if($request->has('unidad')){ 	
	        					$query->where('idUnidad','=',$request->get('unidad'));
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

	public function showEmpPerfilPuesto($idEmp){
		try{
			$idEmpleado = Crypt::decrypt($idEmp);
			$data = ['title' 			=> 'Perfiles de Puesto' 
					,'subtitle'			=> 'Administrador RH'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Perfiles de Puesto', 'url' => route('perfiles.puesto')],
				 		['nom'	=>	'Mostrar', 'url' => '#']
					]]; 
			$data['emp'] = Empleados::findOrFail($idEmpleado);

			return view('pp.personal',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}
	}

	public function mostrarTarea(Request $request,$idEmp,$idTar){
		try{
			$idTarea = Crypt::decrypt($idTar);
			$idEmpleado = Crypt::decrypt($idEmp);

			$tar = Tareas::findOrFail($idTarea);

			$data = ['title' 			=> 'Perfiles de Puesto' 
					,'subtitle'			=> 'Administrador RH'
					,'breadcrumb' 		=> [
				 		['nom'	=>	'Perfiles de Puesto', 'url' => route('perfiles.puesto')],
				 		['nom'	=>	'Mostrar', 'url' => route('perfiles.puesto.emp',['idEmp' => $idEmp])]
					]]; 
			
			$data['emp'] = Empleados::findOrFail($idEmpleado);
			$data['reTar'] = $tar;
			
			return view('pp.tareaShow',$data);
		}catch(ModelNotFoundException $mnfe){
			return view('errors.generic',['error' => 'Algo salio mal, parece que no se ha podido encontrar algunos datos!']);
		}catch(DecryptException $de){
			return view('errors.generic',['error' => 'Algo salio mal, parece que los datos proporcionados no son validos!']);
		}
	}
}
