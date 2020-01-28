<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CatEmpleados;
use App\Models\rrhh\MarcasEmpleado;
use App\Models\rrhh\VwAllPermisos;
use Illuminate\Http\Request;
use App\Models\cssp_usuarios;
use App\User;
use Auth;
use DB;

use App\Models\rrhh\rh\Empleados;

use Validator;
use Debugbar;
use Mail;
use Log;

use DateTime;

class empleadosController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$fInicio = null;
		$fFinal = null;
		if(isset($_POST['fecha']))
		{
			$fechas =$_POST['fecha'];
			$fInicio = $fechas[0];				
			$fFinal = $fechas[1];
		}
		
		
		if(!$fInicio)
		{
			$fInicio = date('Ymd');
		}
		
		if(!$fFinal)
		{
			$fFinal = date('Ymd');	
		}

		
		
		$data = ['title' 			=> 'Empleados: '
				,'subtitle'			=> 'Marcaci&oacute;n',
				'fInicial' => $fInicio,
				'fFinal' => $fFinal,
				]; 
		//dd($data);
		return view('users.marcacion',$data);
	}


	public function marcacionEmpleados()
	{
		if(!(Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->esJefatura())){
				return view('errors.generic',['error' => 'Solo los usuarios con nivel de jefatura pueden ver las marcaciones de sus empleados']);	
		}

		
		
		$data = ['title' 			=> 'Empleados: '
				,'subtitle'			=> 'Marcaci&oacute;n Empleados',
				]; 
		$idPlaza = CatEmpleados::find(Auth::user()->idEmpleado);				
		$empleados=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
								->select(DB::raw('emp.idEmpleado,emp.nombresEmpleado, emp.apellidosEmpleado'))
								->join('dnm_rrhh_si.RH.plazasFuncionales as fn','fn.idPlazaFuncional','=','emp.idPlazaFuncional')
								->where('fn.idPlazaFuncionalPadre',$idPlaza->idPlazaFuncional)
								->where('emp.estadoId',1)
								->get();
		$data['empleados']=$empleados;
		return view('users.marcacionempleados',$data);
	}

	
	
	public function getMarcaciones(Request $request){
		//dd($request->all());

		$marcacionesEmp = MarcasEmpleado::getHistMarcacion();
		$permisos = VwAllPermisos::where('idEmpleadoCrea',Auth::user()->idEmpleado)->whereIn('idEstadoSol',[3,4,6])
					->select('motivo','fechaDesde','fechaHasta')->get()->toArray();
		 //dd($permisos);
		$data=array();
		for($i=0;$i<count($marcacionesEmp);$i++) {
				 if(date('H',strtotime($marcacionesEmp[$i]['FechaMarca']))>=13)
				 {
				 	$data[$i]['title']= date('H:i',strtotime($marcacionesEmp[$i]['FechaMarca'])).' - '.'SALIDA';
				 	$data[$i]['color']='#00CC66';
				 	$time1 = strtotime('16:00:00');
					$time2 = strtotime(date('H:i:s',strtotime($marcacionesEmp[$i]['FechaMarca'])));
					
					$interval = $time1 - $time2;
					
				 	if($interval > 0){
				 		$data[$i]['color']='#ce0024';	

				 	}
				 	else{
				 		$data[$i]['color']='#00CC66';
				 	}
				 }
				 else{
				 	$data[$i]['title']= date('H:i',strtotime($marcacionesEmp[$i]['FechaMarca'])).' - '.'ENTRADA';
				 	$time1 = strtotime('08:00:59');
					$time2 = strtotime(date('H:i:s',strtotime($marcacionesEmp[$i]['FechaMarca'])));
					
					$interval = $time2 - $time1;
					
				 	if($interval > 0){
				 		$data[$i]['color']='#ce0024';	

				 	}
				 	else{
				 		$data[$i]['color']='#0066CC';
				 	}
				 	
				 	
				 }
			 
				$data[$i]['start']= $marcacionesEmp[$i]['FechaMarca'];
		}
		$inicio=count($data);
		//dd($inicio);
		for($j=0;$j<count($permisos);$j++){

				$data[$inicio]['title']= html_entity_decode($permisos[$j]['motivo']);
				$data[$inicio]['color']='#FF8000';
				$data[$inicio]['start']= $permisos[$j]['fechaDesde'];
				$data[$inicio]['end']= $permisos[$j]['fechaHasta'];
			$inicio++;
		}

		//dd($data);
		return json_encode($data);
			//dd($marcacionesEmp);	
		//return $marcacionesEmp;
	}


	public function getMarcacionesByEmpleado(Request $request){
		//dd($request->all());

		$marcacionesEmp = MarcasEmpleado::getHistMarcacionByEmpleado($request->idEmp);
		
		$permisos = VwAllPermisos::where('idEmpleadoCrea',$request->idEmp)->whereIn('idEstadoSol',[3,4,6])
					->select('motivo','fechaDesde','fechaHasta')->get()->toArray();
		 //dd($permisos);
		$data=array();
		for($i=0;$i<count($marcacionesEmp);$i++) {
				 if(date('H',strtotime($marcacionesEmp[$i]['FechaMarca']))>=13)
				 {
				 	$data[$i]['title']= date('H:i',strtotime($marcacionesEmp[$i]['FechaMarca'])).' - '.'SALIDA';
				 	$data[$i]['color']='#00CC66';
				 }
				 else{
				 	$data[$i]['title']= date('H:i',strtotime($marcacionesEmp[$i]['FechaMarca'])).' - '.'ENTRADA';
				 	$data[$i]['color']='#0066CC';
				 	
				 }
			 
				$data[$i]['start']= $marcacionesEmp[$i]['FechaMarca'];
		}
		$inicio=count($data);
		//dd($inicio);
		for($j=0;$j<count($permisos);$j++){

				$data[$inicio]['title']= html_entity_decode($permisos[$j]['motivo']);
				$data[$inicio]['color']='#FF8000';
				$data[$inicio]['start']= $permisos[$j]['fechaDesde'];
				$data[$inicio]['end']= $permisos[$j]['fechaHasta'];
			$inicio++;
		}

		//dd($data);
		return json_encode($data);
			//dd($marcacionesEmp);	
		//return $marcacionesEmp;
	}

	public function vCambioPasswd()
	{
		$data = [
					'title' 			=> 'Usuarios'
					,'subtitle'			=> 'Cambio de Contraseña'
				]; 
		return view('users.cambioPasswd',$data);
	}

	public function checkPasswd(Request $request)
	{
		
		if($request->has('pwdold'))
		{
			$user = User::where('idUsuario',Auth::user()->idUsuario)->get();

			$pwdold = md5($request->pwdold);
			
			if($user[0]->password == $pwdold) 
			{
				return response()->json(['status' => 200, 'message' => 'Password OK'],200);
			}
			else
			{
				return response()->json(['status' => 404, 'message' => 'La contraseña anterior es incorrecta'],200);
			}
		}

	}

	public function cambiarPasswd(Request $request)
	{
		$validate = Validator::make($request->all(),[          
            'pwdold' => 'required',
            'pwdnew1'=>'required',
            'pwdnew2' =>'required',
             ],
            ['pwdnew1.required' => 'La nueva contraseña es obligatoria']);
       
        if ($validate->fails())
        { 
            $msg = "<ul class='text-warning'>";
            foreach ($validate->messages()->all() as $err) {
                $msg .= "<li>$err</li>";
            }
            $msg .= "</ul>";
            return response()->json(['status' => 404,'message' =>$msg],200);
        }
        else
        {
        	DB::connection('transporte')->beginTransaction();
        	try 
        	{
        		$userCssp = cssp_usuarios::where('ID_USUARIO',Auth::user()->idUsuario)->first();

        		$userCssp->PASSWORD = md5($request->pwdnew1);
        		$userCssp->USUARIO_MODIFICACION = Auth::user()->idUsuario;
        		$userCssp->update();

        		$userDnm = User::where('idUsuario',Auth::user()->idUsuario)->first();

        		$userDnm->password = md5($request->pwdnew1);
        		$userDnm->idUsuarioModifica = Auth::user()->idUsuario;
        		$userDnm->update();

        		DB::connection('transporte')->commit();

        		return response()->json(['status' => 200,'message' =>'Se ha actualziado su contraseña<br><b>Recuerde que esta contraseña será utilizada para el acceso a los sistemas de la DNM</b>'],200);	

        	} 
        	catch (\Exception $e) 
        	{
        		DB::connection('transporte')->rollback();

        		Debugbar::addException($e);

        		Log::error($e->getMessage());

        		Mail::raw($e->getMessage(), function ($message) {
        		    
        		    $message->to('rogelio.menjivar@medicamentos.gob.sv', 'Rogelio');
        		
        		    $message->subject('Solicitudes Administrativas - Cambio de Contraseña');
        		
        		    $message->priority(1);
        		        		    
        		});

        		return response()->json(['status' => 404,'message' =>'No fue posible realizar la acción solicitada'],200);	
        		
        	}

        	
        }

	}
}
