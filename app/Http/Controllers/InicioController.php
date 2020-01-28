<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use DB;
use Auth;
use Session;
use App\DiasFeriados;
use App\Models\rrhh\MarcasEmpleado;
use App\CatEmpleados;
use App\CatJefes;
use App\PlazasFuncionales;
use DateTime;
use DatePeriod;
use DateInterval;
use App\User;
use Khill\Lavacharts\Lavacharts as Lavacharts;

class InicioController extends Controller {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');

	}
	/**
	 * Muestra el Inicio de la aplicacion (Dashboard).
	 *
	 * @return Response
	 */
	public function index()
	{	
		
		

		if(Auth::user()->idEmpleado!=null){
		$data = ['title' 			=> 'Inicio' 
				,'subtitle'			=> ''];
		$marcacionesEmp = MarcasEmpleado::where('CodEmpleado','=',Auth::user()->idEmpleado)->whereRaw('month(FechaMarca) = '.date('m'))->whereRaw('year(FechaMarca) = '.date('Y'))->orderby('FechaMarca','DESC')->get();
		
		$idPlazaFuncional=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
						 ->where('idEmpleado',Auth::user()->idEmpleado)
						 ->select('idPlazaFuncional')
						 ->first();

		if($idPlazaFuncional!=null){
			//dd('hola');
			$idPlazaFuncionalPadre=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.plazasFuncionales')
								->where('idPlazaFuncional',$idPlazaFuncional->idPlazaFuncional)
								->select('idPlazaFuncionalPadre')->first();
			//dd($idPlazaFuncionalPadre);
			if($idPlazaFuncionalPadre->idPlazaFuncionalPadre==='0' or $idPlazaFuncionalPadre->idPlazaFuncionalPadre==null){
				//dd('hello');
				$idPlazaFuncionalPadre=null;
				$jefes=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.jefes as je')
				->join('dnm_rrhh_si.RH.plazasFuncionales as plf','je.idPlazaFuncional','=','plf.idPlazaFuncional')
				->select('plf.idPlazaFuncional','plf.nombrePlaza')
				->get();
				//dd($data);
			}
			else{
				$idPlazaFuncionalPadre=$idPlazaFuncionalPadre->idPlazaFuncionalPadre;
				$jefes=null;
			}
		}


		$idPlaza = CatEmpleados::find(Auth::user()->idEmpleado);
		//dd($idPlaza);
		$unidadEmpleado = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.unidades as uni')
							->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idUnidad','=','uni.idUnidad')
							->where('fn.idPlazaFuncional',$idPlaza->idPlazaFuncional)
							->first();

		$marcacionesUnidad = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.marcacion as reloj')
							->join('dnm_rrhh_si.RH.empleados as emp','emp.idEmpleado','=','reloj.CodEmpleado')
							->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
							->join('dnm_rrhh_si.RH.unidades as uni','uni.idUnidad','=','fn.idUnidad')
							->where('fn.idPlazaFuncionalPadre',$idPlaza->idPlazaFuncional)
							->whereRaw('month( reloj.FechaMarca ) = '.date('m'))
							->whereRaw('year(reloj.FechaMarca) = '.date('Y'))
							->orderBy('reloj.CodEmpleado')
							->orderBy('reloj.FechaMarca','DESC')
							->get();
		

		
		$data['marcaciones'] =$marcacionesEmp;
		    $annio = Date('Y');
			$dashboardEmpleados = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
									->select(DB::raw('emp.idEmpleado,emp.nombresEmpleado, emp.apellidosEmpleado,fn.nombrePlaza,uni.nombreUnidad,isnull(lic.cantidad,0) as cantidad'))
									->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
									->join('dnm_rrhh_si.RH.unidades as uni','uni.idUnidad','=','fn.idUnidad')
									->leftJoin(DB::raw('(Select idEmpleadoCrea,count(*) as cantidad from dnm_rrhh_si.Permisos.solicitudLicencia where YEAR(fechaCreacion) = '.$annio.' group by idEmpleadoCrea) as lic'),'lic.idEmpleadoCrea','=','emp.idEmpleado')
									->where('fn.idPlazaFuncionalPadre',$idPlaza->idPlazaFuncional)
                                    ->where('emp.estadoId',1)
								->get();

		//dd($dashboardEmpleados);

		$chartSolicitudes = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
								->select(DB::raw('emp.nombresEmpleado + \'\'+ emp.apellidosEmpleado as nombreEmpleado,isnull(lic.cantidad,0) as cantidad'))
								->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
								->join('dnm_rrhh_si.RH.unidades as uni','uni.idUnidad','=','fn.idUnidad')
								->leftJoin(DB::raw('(Select idEmpleadoCrea,count(*) as cantidad from dnm_rrhh_si.Permisos.solicitudLicencia where YEAR(fechaCreacion) = '.$annio.' group by idEmpleadoCrea) as lic'),'lic.idEmpleadoCrea','=','emp.idEmpleado')
								//->where('uni.idUnidad',$unidadEmpleado->idUnidad)
								->where('fn.idPlazaFuncionalPadre',$idPlaza->idPlazaFuncional)
                                ->where('emp.estadoId',1)
								->get();

		$chartSolicitudesUnidades =DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
								->select(DB::raw('uni.nombreUnidad,sum(isnull(lic.cantidad,0)) as cantidad'))
								->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
								->join('dnm_rrhh_si.RH.unidades as uni','uni.idUnidad','=','fn.idUnidad')
								->leftJoin(DB::raw('(Select idEmpleadoCrea,count(*) as cantidad from dnm_rrhh_si.Permisos.solicitudLicencia where YEAR(fechaCreacion) = '.$annio.' group by idEmpleadoCrea) as lic'),'lic.idEmpleadoCrea','=','emp.idEmpleado')
								->groupBy('uni.nombreUnidad')
								->get();

		

		$nombreUnidadJefatura = null;
		$title = 'SOLICITUDES ADMINISTRATIVAS';
		if(CatJefes::where('idPlazaFuncional',CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional)->first()!=null)
		{
			if(!empty($dashboardEmpleados)){
				$nombreUnidadJefatura = $dashboardEmpleados[0]->nombreUnidad;
				$title = 'DASHBOARD '.mb_strtoupper($dashboardEmpleados[0]->nombreUnidad);
			}
		}
		
		$lava = new Lavacharts;
		$dtSolicitudes = $lava->DataTable();

		$dtSolicitudes->addStringColumn('Empleado')
		         ->addNumberColumn('Solicitudes')
		         ->addRoleColumn('string','annotation');
		foreach ($chartSolicitudes as $key => $value) {
			$dtSolicitudes->addRow([mb_strtoupper($value->nombreEmpleado,'utf-8'),$value->cantidad,$value->cantidad]);
		}

		$lava->ColumnChart('solicitudes', $dtSolicitudes, [
			'title' => '',
			'titleTextStyle' => [
			'color'    => '#eb6b2c',
			'fontSize' => 11
			],
			'legend' => 'none',
			'height' => 540,
			'width' => 1024,
			'hAxis' => ['title' => 'Dependientes','textStyle' => ['fontSize' => 9]],
			'vAxis' => ['title' => 'Cantidad de Soliciudes','minValue' => 0, 'maxValue' => 50],
			'animation' => ['duration' => 1000,'easing' => 'out']
		]);

		$dtSolicitudesUnidades = $lava->DataTable();

		$dtSolicitudesUnidades->addStringColumn('Unidad')
						->addNumberColumn('Solicitudes')
						->addRoleColumn('string','annotation');

		foreach ($chartSolicitudesUnidades as $key => $value) {
			$dtSolicitudesUnidades->addRow([mb_strtoupper($value->nombreUnidad,'utf-8'),$value->cantidad,$value->cantidad]);
		}

		$lava->ColumnChart('solicitudesUnidades', $dtSolicitudesUnidades, [
			'title' => '',
			'titleTextStyle' => [
			'color'    => '#eb6b2c',
			'fontSize' => 24
			],
			'legend' => 'none',
			'height' => 540,
			'width' => 1024,
			'hAxis' => ['title' => 'Unidades DNM','textStyle' => ['fontSize' => 9]],
			'vAxis' => ['title' => 'Cantidad de Soliciudes','minValue' => 0, 'maxValue' => 50],
			'Animation' => ['duration' => 1000,'easing' => 'out']
		]);

		$data = ['title' 			=> $title
				,'subtitle'			=> '',
				'marcaciones' => $marcacionesEmp,
				'dashboard' => $dashboardEmpleados,
				'unidadJefatura' => $nombreUnidadJefatura,
				'lava' => $lava,
				'marcacionUnidad' => $marcacionesUnidad,
				'jefes' => $jefes,
				'idPlazaFuncionalPadre' => $idPlazaFuncionalPadre
				];

		//dd($data);
		return view('inicio.index',$data);
		}
		else{
			$data = ['title' 			=> 'ACTUALIZACION DE DATOS'
				,'subtitle'			=> '',
				];
			$data['idPlazaFuncionalPadre']=null;
			$data['jefes']=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.jefes as je')
								->join('dnm_rrhh_si.RH.plazasFuncionales as plf','je.idPlazaFuncional','=','plf.idPlazaFuncional')
								->select('plf.idPlazaFuncional','plf.nombrePlaza')
								->get();
			//dd($data);
			return view('inicio.index',$data);
		}
	}

	public static function actualizarDatos(Request $request){
			//dd($request->all());
		if($request->jefeI==='0'){
				Session::flash('msnError', 'Debe seleccionar quien es su jefe inmediato!');
					return redirect()->route('doInicio');
		}

		if($request->has('codigo')){
			$usuario=User::find(Auth::user()->idUsuario);
			$usuario->idEmpleado=$request->codigo;
			$usuario->correo=$request->correo;
			//$usuario->fechaModificacion=date('Y-m-d');
			if($usuario->save()){
				if($request->has('jefeI')){
					$empleado=CatEmpleados::find($usuario->idEmpleado);
					$plazaFuncional=PlazasFuncionales::find($empleado->idPlazaFuncional);
					$plazaFuncional->idPlazaFuncionalPadre=$request->jefeI;
					$plazaFuncional->save();
				}
				Session::flash('msnExito', 'SE HAN ACTUALIZADO SUS DATOS CORRECTAMENTE.!');
				return redirect()->route('doInicio');
			}
			else{
				Session::flash('msnError', 'PROBLEMAS CON EL SERVIDOR, NO SE HA PODIDO ACTUALIZAR SUS DATOS.!');
				return redirect()->route('doInicio');
			}
		}
		elseif($request->has('jefeI')){
			if($request->jefeI==='0'){
				Session::flash('msnError', 'Debe seleccionar quien es su jefe inmediato!');
					return redirect()->route('doInicio');
			}
			else{
				$empleado=CatEmpleados::find(Auth::user()->idEmpleado);
				$plazaFuncional=PlazasFuncionales::find($empleado->idPlazaFuncional);
				$plazaFuncional->idPlazaFuncionalPadre=$request->jefeI;
				if($plazaFuncional->save()){
					Session::flash('msnExito', 'SE HAN ACTUALIZADO SUS DATOS CORRECTAMENTE.!');
					return redirect()->route('doInicio');
				}
				else{
					Session::flash('msnError', 'PROBLEMAS CON EL SERVIDOR, NO SE HA PODIDO ACTUALIZAR SUS DATOS.!');
					return redirect()->route('doInicio');
				}
			}

		}
						
	}
	/**
	 * Cambia configuración para ocultar menú lateral.
	 *
	 * @return void
	 */
	public function cfgHideMenu()
	{
		$cfgHideMenu = Session::get('cfgHideMenu',false);
		$cfgHideMenu = ($cfgHideMenu)?false:true;
		Session::put('cfgHideMenu',$cfgHideMenu);
	}
	
	public static function getWorkingDays($endDate,$startDate) {
			//return 'hola';
			//return $request->all();
		
		
		    //$endDate = strtotime($request->get('fechaFin'));
		    //$startDate = strtotime($request->get('fechaInicio'));
		    $feriados = DiasFeriados::where('ano',date('Y'))->get();
		    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
		    //We add one to inlude both dates in the interval.
			//if($startDate >= strtotime(date('Y-m-d'))){
				if($endDate >= $startDate){
					$days = ($endDate - $startDate) / 86400 + 1;
					$no_full_weeks = floor($days / 7);
					$no_remaining_days = fmod($days, 7);
					//It will return 1 if it's Monday,.. ,7 for Sunday
					$the_first_day_of_week = date("N", $startDate);
					$the_last_day_of_week = date("N", $endDate);
					//---->The two can be equal in leap years when february has 29 days, the equal sign is added here
					//In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
					if ($the_first_day_of_week <= $the_last_day_of_week) {
						if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
						if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
					}
					else {
						// (edit by Tokes to fix an edge case where the start day was a Sunday
						// and the end day was NOT a Saturday)
						// the day of the week for start is later than the day of the week for end
						if ($the_first_day_of_week == 7) {
							// if the start date is a Sunday, then we definitely subtract 1 day
							$no_remaining_days--;
							if ($the_last_day_of_week == 6) {
								// if the end date is a Saturday, then we subtract another day
								$no_remaining_days--;
							}
						}
						else {
							// the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
							// so we skip an entire weekend and subtract 2 days
							$no_remaining_days -= 2;
						}
					}
					//The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
				//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
				   $workingDays = $no_full_weeks * 5;
					if ($no_remaining_days > 0 )
					{
					  $workingDays += $no_remaining_days;
					}
				   
					$holidays=[];
					foreach($feriados as $feriado){
						if(count(json_decode($feriado->dia))>1){
							for($i=0;$i<count(json_decode($feriado->dia));$i++){
								array_push($holidays, date('Y').'-'.json_decode($feriado->dia)[$i]);
							}
						}
						else{
							array_push($holidays, date('Y').'-'.json_decode($feriado->dia)[0]);
						}

					}
					
					//$holidays=array("2016-11-02");
					//We subtract the holidays
					foreach($holidays as $holiday){
						
						$time_stamp=strtotime($holiday);
						//If the holiday doesn't fall in weekend
						if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
						//        return date('N',$time_stamp);
						   $workingDays--;
					}
				 
					return response()->json(['status' => 200,'message' => "Se han validado la fechas",'data' => (int)$workingDays]);
				}
				else{
					return response()->json(['status' => 400,'message' => "La fecha Fin no puede ser menor que la fecha de Inicio"]);
				}
		    //}
			//else{
			//	return response()->json(['status' => 400,'message' => "La fecha Inicio no puede ser menor que la fecha de hoy"]);
			//}
			
	}
}