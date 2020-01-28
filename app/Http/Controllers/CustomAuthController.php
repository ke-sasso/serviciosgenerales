<?php namespace App\Http\Controllers;

use App;
use Auth;  
use Session; 
use App\User;
use App\UserOptions;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SolLicencia;
use App\SolNoMarcacion;
use App\SolicitudMotivo;
use DB;
use App\CatMotivos;
use App\CatEmpleados;
use App\Unidades;
use App\Cat_EstadoS;
use Illuminate\Http\Request;

class CustomAuthController extends Controller {

	public function getLogin(){
		$data = ['title' 			=> 'Inicio'
				,'subtitle'			=> ''];
		//Verificamos si ya esta logueado de lo contrario se redirige al login
		if(Auth::check()){
			return redirect()->route('doInicio'); 
		}else{
			return view('users.login'); 	
		}
    }  
  	
    public function postLogin(App\Http\Requests\InicioSesionRequest $request) {  
        //Obtenemos el usuario que se loguea si es que existe
        //dd($request->all());
        $user = User::where('idUsuario', $request->txtUsuario)
        		->where('password', md5($request->txtContrasenia))
        		->where('activo', 'A')->first();

        //dd($user);
        //Verificamos si el usuario existe y cumple las condiciones
        if($user){
            if(UserOptions::verifyOption($request->txtUsuario,442)){
		    	//Guardamos Logueamos al usuario
			    Auth::login($user);
			    //Redireccion a ruta inicial
			    return redirect()->route('doInicio');
		    }else{
		    	return redirect()->route('doLogin')->withErrors(['error' => 'No tiene permisos para acceder al sistema!']);  
		    }
		}else{
		 	return redirect()->route('doLogin')->withErrors(['error' => 'Usuario y/o Contraseña Invalidos!']);  
		}
    }  
    public function getLogout()
	{
		//Deslogueamos al usuario
		Auth::logout();
		//Eliminamos de session la variable
		Session::forget('PERMISOS');
		//Redireccion a ruta inicial
		return redirect()->route('doLogin');
	}
	
	
	public function autorizacion2($idTipo,$idSolicitud,$idEstado){
		
		// si es tipo 1 es solicitud de no marcacion
		if($idTipo==1){
			//busca la solicitud de no marcacion			
			$solnomarcacion=SolNoMarcacion::find($idSolicitud);
			//verifica el estado si tiene estado de ingresada, actualiza el estado
			if($solnomarcacion->idEstado==1){
				//busca el jefe inmediato para autorizar la solicitud
				$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
							->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
									from dnm_rrhh_si.RH.plazasFuncionales where 
									idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.$solnomarcacion->idEmpleadoCrea.'))'))
							->first();
				// se obtiene los datos para mostrar lo que se autorizo 			
				$data['solicitante']=User::where('idEmpleado',$solnomarcacion->idEmpleadoCrea)->first();
				$solmotv=SolicitudMotivo::find($solnomarcacion->motivo);
				$motivo=CatMotivos::find($solmotv->idMotivo);
				$empleado=CatEmpleados::find($solnomarcacion->idEmpleadoCrea);
				$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
				$data['motivo']=$motivo;
				$data['unidad']=$unidad;
				$data['autorizada']=1;
				
				// se le cambia el estado a la solicitud ya sea denegada o autorizada
				$solnomarcacion->autorizacion1=$jefeInmediato->idPlazaFuncional;
				$solnomarcacion->idEstado=$idEstado;
				//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
				$solnomarcacion->save();
				$sol=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudNoMarcacion as soln')
					->join('dnm_rrhh_si.Permisos.estadoSolicitud as est','soln.idEstado','=','est.idEstadoSol')
					->select('est.*','soln.*')
					->where('idSolNoMarca','=',$idSolicitud)->first();
					
				$data['solicitud']=$sol;
				//dd($solnomarcacion);
				//dd($data);
				return view ('emails.confirmacion',$data);
			}
			else{
				// si no tiene idEstado igual a 0 solo se muestra el tipo de estado que tiene la solicitud,
				//ya sea autorizada o denegada.
				$sol=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudNoMarcacion as soln')
					->join('dnm_rrhh_si.Permisos.estadoSolicitud as est','soln.idEstado','=','est.idEstadoSol')
					->select('est.*','soln.*')
					->where('idSolNoMarca','=',$idSolicitud)->first();
					
				$data['solicitud']=$sol;
				$data['autorizada']=0;
				return view ('emails.confirmacion',$data);
			}
		}
		elseif($idTipo==2){
			
			$sollicencia=SolLicencia::find($idSolicitud);
			if($sollicencia->idEstado==1 || $idEstado==4 ||  $idEstado==6 || $idEstado==2){
				//busca el jefe inmediato para autorizar la solicitud
				$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
							->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
									from dnm_rrhh_si.RH.plazasFuncionales where 
									idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.$sollicencia->idEmpleadoCrea.'))'))
							->first();
				if($idEstado==3 || $idEstado==4 || $idEstado==6){
					$sollicencia->autorizacion2=$jefeInmediato->idEmpleado;
				}
				else{
					$sollicencia->autorizacion1=$jefeInmediato->idEmpleado;
				}
				$sollicencia->idEstado=$idEstado;
				//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
				$sollicencia->save();
				
				//dd($sollicencia);
				$data['solicitante']=User::where('idEmpleado',$sollicencia->idEmpleadoCrea)->first();
				$solmotv=SolicitudMotivo::find($sollicencia->enConcepto);
				$motivo=CatMotivos::find($solmotv->idMotivo);
				$empleado=CatEmpleados::find($sollicencia->idEmpleadoCrea);
				$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
				$data['motivo']=$motivo;
				$data['unidad']=$unidad;
				$sol=DB::connection('sqlsrv')->
					  table('dnm_rrhh_si.Permisos.solicitudLicencia as sol')
					  ->join('dnm_rrhh_si.Permisos.estadoSolicitud as est','sol.idEstado','=','est.idEstadoSol')
					  ->where('idSolLicencia','=',$idSolicitud)->first();
				$data['solicitud']=$sol;
				$data['autorizada']=1;
				//dd($data);
				return view ('emails.confirmacion',$data);
			}
			else{
				$data['autorizada']=0;
				$sol=DB::connection('sqlsrv')->
					  table('dnm_rrhh_si.Permisos.solicitudLicencia as sol')
					  ->join('dnm_rrhh_si.Permisos.estadoSolicitud as est','sol.idEstado','=','est.idEstadoSol')
					  ->where('idSolLicencia','=',$idSolicitud)->first();
				$data['solicitud']=$sol;
				//dd($data);
				return view ('emails.confirmacion',$data);
			}
		}
		
	}
	
}
