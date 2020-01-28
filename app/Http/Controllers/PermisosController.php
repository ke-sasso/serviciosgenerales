<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests\SeguroRequest;
use Session;
use Redirect;
use App\CatEmpleados;
use App\Models\rrhh\MarcasEmpleado;
use App\SolLicencia;
use App\SolNoMarcacion;
use App\SolSeguro;
use Mail;
use App\VwPermisos;
use App\Models\rrhh\VwAllPermisos;
use yajra\Datatables\Datatables;
use App\User;
use App\SolicitudMotivo;
use App\Unidades;
use App\CatMotivos;
use App\CatJefes;
use App\Models\rrhh\CatDependientes;
use App\Models\rrhh\CatEnfermedades;
use App\Http\Controllers\InicioController;
use App\Models\rrhh\CapitulosEnfermedades;
use App\Models\rrhh\DocumentoSeguro;
use File;
use Crypt;
use Carbon\Carbon;
use Response;
use DateTime;
use Illuminate\Filesystem\Filesystem;
class PermisosController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 public function __construct(){
        $this->middleware('auth');
    }
	
	public function getNoMarcacion()
	{
		//
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		$empleado=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
		->join('dnm_rrhh_si.RH.plazasFuncionales as func','emp.idPlazaFuncional','=','func.idPlazaFuncional')
		->join('dnm_rrhh_si.RH.unidades as uni','func.idUnidad','=','uni.idUnidad')
		->where('emp.idEmpleado',Auth::user()->idEmpleado)->first();
		
		$data['empleado']=$empleado;
		$motivos=SolicitudMotivo::getMotivosBySolicitud(1);
		$data['motivos']=$motivos;
		//dd($data);
		//return $data;
		return view('solicitudes.permisos.nomarcacion',$data);
	}
   public function segurosEmpleados(){
		$data = ['title' 			=> 'SOLICITUDES DE SEGUROS'
				,'subtitle'			=> '>EMPLEADOS'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Solicitudes', 'url' => '#'],
			 		['nom'	=>	'Seguros', 'url' => '#']
				]]; 

	  $data['unidades']=Unidades::all();
	  $data['motivos']=CatMotivos::where('idMotivo',5)
	    ->orWhere('idMotivo', 33)
	    ->orWhere('idMotivo', 4)
	    ->orWhere('idMotivo', 32)
	    ->orWhere('idMotivo', 34)
	    ->orWhere('idMotivo', 29)
	    ->orWhere('idMotivo', 30)
	    ->get();
		return view('solicitudes.permisos.segurosDNM',$data);

	}
	public function mostrarSeguroDNM($idSolSeguro)
	{
		$solseguro=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudSeguros')
		->where('idSolSeguro',Crypt::decrypt($idSolSeguro))->first();
		if($solseguro->idDependiente==0){
			$asegurado=CatEmpleados::find($solseguro->idAsegurado);
			$presentado=$asegurado->nombresEmpleado.' '.$asegurado->apellidosEmpleado;
		}
		elseif($solseguro->idAsegurado==0){
			$depen=CatDependientes::find($solseguro->idDependiente);
			$presentado=$depen->nombres;
		}		
		$motivos=SolicitudMotivo::getMotivosBySolicitud(1);
		$documento=DocumentoSeguro::where('idSolSeguro',$solseguro->idSolSeguro)->first();
		$data = ['title' 			=> 'SOLICITUD DEL SEGURO'
				,'subtitle'			=> '>EMPLEADO'
				,'breadcrumb' 		=> [

			 		['nom'	=>	'Regresar a solicitudes', 'url' =>  route('all.seguros.dnm')],
			 		['nom'	=>	'Solicitud del empleado', 'url' => '#']
				]]; 
		$data['motivos']=$motivos;
        $data['presentado']=$presentado;
		$data['solicitud']=$solseguro;
		$data['documento']=$documento;
		return view('solicitudes.permisos.seguroEmpleado',$data);
	}

	 public function getDataRowsSegurosDNM(Request $request){
   		//dd($request->all());
		$seguros=SolSeguro::getSegurosDNM();
		return Datatables::of($seguros)

			->addColumn('detalle', function ($dt) {

						return	'<a href="'.route('ver.seguro.empleado',['idSolSeguro' =>Crypt::encrypt($dt->idSolSeguro)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-plus-square" aria-hidden="true"></i></a>';
                        
					 			
						})

			->addColumn('nombreEstado',function($dt){
						if($dt->idEstadoSeguro == 1)
							return '<span class="label label-success "> '.$dt->nombreEstado.'<i class="fa fa-check"></span>';
						else if($dt->idEstadoSeguro== 2)
							return '<span class="label label-info">'.$dt->nombreEstado.'<i class="fa fa-check-circle"></span>';
						else if($dt->idEstadoSeguro == 3)
							return '<span class="label label-success">'.$dt->nombreEstado.'<i class="fa fa-ckeck"></span>';
						else if($dt->idEstadoSeguro== 4)
							return '<span class="label label-success">'.$dt->nombreEstado.'<i class="fa-check-circle"></span>';
						else if($dt->idEstadoSeguro== 5)
							return '<span class="label label-warning">'.$dt->nombreEstado.'<i class="fa fa-check-circle"></span>';
						else if($dt->idEstadoSeguro == 6)
							return '<span class="label label-danger">'.$dt->nombreEstado.'<i class="fa fa-times"></span>';
					
					})
          
               ->filter(function($query) use ($request){
							
	        				if($request->has('unidad')){
	        					$query->where('uni.idUnidad','=',(int)$request->get('unidad'));
	        				}
	        				
	        				if($request->has('fechaInicio')){ 	
	        					$query->where(DB::raw('Convert(varchar(10), se.fechaCreacion,120)'),'=',date('Y-m-d',strtotime(str_replace("/","-",$request->get('fechaInicio')))));
	        				}

	        				if($request->has('procesada')){
	        					$query->where('se.idEstadoSeguro','=',(int)$request->get('procesada'));
	        				}
	        				if($request->has('tipo')){
	        					$query->where('se.idMotivo','=',(int)$request->get('tipo'));
	        				}
							

	        			})
     
			->make(true);
	
	}
	
	public function mostrarSolicitud($idTipo,$idSolicitud){
		
		//dd($tipo);
		if($idTipo==1){
			return $this->showNoMarcacion($idSolicitud);
		}
		elseif(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20 || CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==19){
			if($idTipo==2){
				return $this->showLicenciaAutorizar($idSolicitud);
			}
		}
		elseif($idTipo==2){
			//return 'hola';
			return $this->showLicencia($idSolicitud);
		}
		
	}
	public function showNoMarcacion($idSolicitud)
	{
		$solicitud=SolNoMarcacion::find($idSolicitud);
		$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
		$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
		
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 

		//$data['solicitud']=$solicitud;
		$data['empleado']=$empleado;
		$data['unidad']=$unidad;

		$autorizar = CatJefes::where('idPlazaFuncional',CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional)->first();
		if($autorizar!=null)
			$data['autorizar']=1;
		else
			$data['autorizar']=0;
		
		$sol=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudNoMarcacion')->where('idSolNoMarca','=',$idSolicitud)->first();
		$data['solicitud']=$sol;
		//dd($sol->horaSalida);
		//dd(date('Y-d-m',strtotime($solicitud->fechaCreacion)));
		return view('solicitudes.permisos.shownomarcacion',$data);
	}
	
	public function showLicencia($idSolicitud)
	{	
		//
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 

		$solicitud=SolLicencia::find($idSolicitud);
		//dd($solicitud);
		$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
		$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
		$motivos=SolicitudMotivo::getMotivosBySolicitud(2);
		$data['motivos']=$motivos;
		$data['solicitud']=$solicitud;
		$data['empleado']=$empleado;
		$data['unidad']=$unidad;
		$autorizar = CatJefes::where('idPlazaFuncional',CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional)->first();
		if($autorizar!=null)
			$data['autorizar']=1;
		else
			$data['autorizar']=0;
		
		$data['documento']=DocumentoSeguro::where('idSolLicencia',$idSolicitud)->first();

		return view('solicitudes.permisos.showlicencia',$data);
	}
	
	public function showLicenciaAutorizar($idSolicitud)
	{	
		//
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 
		$solicitud=SolLicencia::find($idSolicitud);
		//dd($solicitud);
		$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
		$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
		$motivos=SolicitudMotivo::getMotivosBySolicitud(2);
		$data['motivos']=$motivos;
		$data['solicitud']=$solicitud;
		$data['empleado']=$empleado;
		$data['unidad']=$unidad;
		$autorizar = CatJefes::where('idPlazaFuncional',CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional)->first();
		if($autorizar!=null)
			$data['autorizar']=1;
		else
			$data['autorizar']=0;
		//dd($data);
		return view('solicitudes.permisos.showlicenciadirector',$data);
	}

	public function mostrarSeguro($idSolSeguro)
	{	//dd($idSolSeguro);
		$solseguro=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudSeguros')
		->where('idSolSeguro',Crypt::decrypt($idSolSeguro))->first();
		//dd($solseguro);
		if($solseguro->idDependiente==0){
			$asegurado=CatEmpleados::find($solseguro->idAsegurado);
			$presentado=$asegurado->nombresEmpleado.' '.$asegurado->apellidosEmpleado;
		}
		elseif($solseguro->idAsegurado==0){
			$depen=CatDependientes::find($solseguro->idDependiente);
			$presentado=$depen->nombres;
		}		
		$motivos=SolicitudMotivo::getMotivosBySolicitud(3);
		$documento=DocumentoSeguro::where('idSolSeguro',$solseguro->idSolSeguro)->first();
		//
		$data = ['title' 			=> 'Permisos y Seguro: '
				,'subtitle'			=> 'Solicitudes de Seguro'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Solicitudes de Seguro', 'url' => '#']
				]]; 
		$data['motivos']=$motivos;
        $data['presentado']=$presentado;
		
		$data['solicitud']=$solseguro;
		$data['documento']=$documento;
		//dd($data);
		return view('solicitudes.permisos.showseguro',$data);
	}
	
	
	public function autorizacion(Request $request){
		//dd($request->all());
		//dd(Auth::user()->idEmpleado);
		if($request->tipoPermiso==1){
			$solnomarcacion=SolNoMarcacion::find($request->idSolicitud);
			$solnomarcacion->autorizacion1=Auth::user()->idEmpleado;
			$solnomarcacion->idEstado=3;
			$solnomarcacion->fechaApruebaDenegar = date('Y-m-d H:i:s');
			//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
			$solnomarcacion->save();
			//dd($solnomarcacion);
			if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==19 or CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20){
				return redirect()->route('all.licencias.director');
			}
			else{
				return redirect()->route('all.permisos.unidad');
			}
			//return redirect()->route('all.permisos.unidad');
		}
		elseif($request->tipoPermiso==2){
			$solnomarcacion=SolLicencia::find($request->idSolicitud);
			$solnomarcacion->autorizacion1=Auth::user()->idEmpleado;
			$solnomarcacion->idEstado=3;
			$solnomarcacion->fechaApruebaDenegar = date('Y-m-d H:i:s');
			//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
			$solnomarcacion->save();
			//dd($solnomarcacion);
			return redirect()->route('all.permisos.unidad');
		}
	}

	public function autorizacionSuperior(Request $request){
		//dd($request->all());
		if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20){
			$solnomarcacion=SolLicencia::find($request->idSolicitud);
			$solnomarcacion->autorizacion1=Auth::user()->idEmpleado;
			$solnomarcacion->idEstado=6;
			$solnomarcacion->fechaApruebaDenegar = date('Y-m-d H:i:s');
			$solnomarcacion->save();
			return redirect()->route('all.licencias.director');
		}
		elseif(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==19){
			$solnomarcacion=SolLicencia::find($request->idSolicitud);
			$solnomarcacion->autorizacion1=Auth::user()->idEmpleado;
			$solnomarcacion->fechaApruebaDenegar = date('Y-m-d H:i:s');
			$solnomarcacion->idEstado=4;
			$solnomarcacion->save();
			return redirect()->route('all.licencias.director');
			//dd($solnomarcacion);
		}
	}

	public function getLicencia()
	{
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		$empleado=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
		->join('dnm_rrhh_si.RH.plazasFuncionales as func','emp.idPlazaFuncional','=','func.idPlazaFuncional')
		->join('dnm_rrhh_si.RH.unidades as uni','func.idUnidad','=','uni.idUnidad')
		->where('emp.idEmpleado',Auth::user()->idEmpleado)->first();
		$data['empleado']=$empleado;
		$motivos=SolicitudMotivo::getMotivosBySolicitud(2);
		$data['motivos']=$motivos;
		//dd($data);
		return view('solicitudes.permisos.licencia',$data);
	}

	public function getSeguro()
	{
		$data = ['title' 			=> 'Permisos: '
				,'subtitle'			=> 'Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Permisos', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		$empleado=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
		->join('dnm_rrhh_si.RH.plazasFuncionales as func','emp.idPlazaFuncional','=','func.idPlazaFuncional')
		->join('dnm_rrhh_si.RH.unidades as uni','func.idUnidad','=','uni.idUnidad')
		->where('emp.idEmpleado',Auth::user()->idEmpleado)->first();
		//dd($empleado);
		$data['empleado']=$empleado;
		$motivos=SolicitudMotivo::getMotivosBySolicitud(3);
		$dependientes=CatDependientes::where('idEmpleado',Auth::user()->idEmpleado)->get();
		$data['dependientes']=$dependientes;
		$data['motivos']=$motivos;
		$capitulos=CapitulosEnfermedades::all();
		$data['capitulos']=$capitulos;
		//dd(CatEmpleados::all()->[0]);
		//dd($data);
		return view('solicitudes.permisos.seguro',$data);
	}
	
	public function getEnfermedades(Request $request){
		//dd($request->all()); 
		//return 'hola';
		$enfermedades= DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.enfermedades')->where('idCapitulo',$request->capitulo)->get();
		//return $enfermedades[0]->nombreEnfermedad;
		return response()->json(['status' => 200,'message' => "",'data' => $enfermedades]);
		
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeSeguro(SeguroRequest $request)
	{	
		
		$asegurado=explode(':',$request->presentado);
		DB::connection('sqlsrv')->beginTransaction();
	    try {
		$seguro = new SolSeguro();
		if($asegurado[0]==1){
			$seguro->idAsegurado=$asegurado[1];
			$seguro->idDependiente=0;
		}
		if($asegurado[0]==2){
			$seguro->idAsegurado=0;
			$seguro->idDependiente=$asegurado[1];
		}
		$seguro->idMotivo=$request->motivo;		
		$seguro->dolencia=$request->det1;
		$seguro->tratado=$request->det2;
		$seguro->idEstadoSeguro=1;
		$seguro->idEmpleadoCrea=Auth::user()->idEmpleado;
		$seguro->idUsuarioCrea=Auth::user()->idUsuario.'@'.$request->ip();
		//dd($seguro);
		$seguro->save();
		//dd($seguro);
		
		$idSeguro=$seguro->idSolSeguro;
		//$path='C:\Seguros';
		$duiUser=Auth::user()->Empleado()->first()->dui;
		
		if(empty($duiUser) || $duiUser==null){
			DB::connection('sqlsrv')->rollback();
		    Session::flash('msnError', 'Su informacion personal no esta completa, debe completarla para poder guardar sus solicitudes.');
			return redirect()->route('seguro');
		}
		
		$newPath='Y:\ECM\urh\eepl'.'\\'.$duiUser;
		//$path='U:\Seguros';
		$file= $request->file('file');
		//primero verifica si hay archivos a subir
		if(!empty($file)){
			//si hay archivos crear la ruta con el id del usuario
			$filesystem= new Filesystem();
		   if($filesystem->exists($newPath)){
			    if($filesystem->isWritable($newPath)){
				$carpeta=$newPath.'\\'.'Seguros';
				//crea la nueva carpeta
				File::makeDirectory($carpeta, 0777, true, true);
				// se guadarn en el disco 
				//dd($file->getClientOriginalName());
				//$name= Auth::user()->apellidosUsuario .$file1->getClientOriginalName();
				$name= 'Seguro#'.$idSeguro.Auth::user()->idEmpleado.'.'.$file->getClientOriginalExtension();
				$type = $file->getMimeType();
				$file->move($carpeta,$name);
				
				//se enlanza cada archivo a su bitacora en la tabla ArchivoBitacora
				$archivo = new DocumentoSeguro();
				$archivo->urlDocumento=$carpeta.'\\'.$name;	
				$archivo->tipoDocumento=$type;
				$archivo->idSolSeguro=$idSeguro;
				$archivo->idUsuarioCrea= Auth::user()->idUsuario;
				$archivo->save();
			   }
			   else{
			   		DB::connection('sqlsrv')->rollback();
				    Session::flash('msnError', 'PROBLEMAS CON EL SERVIDOR, NO SE HA PODIDO GUARDAR EL DOCUMENTO DE SU SOLICITUD, VUELVA A INGRESAR SU SOLICITUD!');
					return redirect()->route('seguro');
			   }
		  }
		  else{
		  	 DB::connection('sqlsrv')->rollback();
		    Session::flash('msnError', 'PROBLEMAS CON EL SERVIDOR, NO SE HA PODIDO GUARDAR EL DOCUMENTO DE SU SOLICITUD, VUELVA A INGRESAR SU SOLICITUD!');
			return redirect()->route('seguro');
		  }

			/*
			if($archivo->save()){
				DB::commit();
				Session::flash('msnExito', 'SE INGRESADO  LA SOLICITUD CORRECTAMENTE!');
				return redirect()->route('seguro');
			}
			else{
				DB::rollback();
				Session::flash('msnError', 'PROBLEMAS CON EL SERVIDOR, NO SE HA PODIDO GUARDAR SU SOLICITUD!');
				return redirect()->route('seguro');

			}*/
						
		}

	}
	catch(Exception $e){
		    DB::connection('sqlsrv')->rollback();
		    throw $e;
		    return $e;
		    Session::flash('msnError', $e->getMessage());
			return redirect()->route('seguro');
	}
	DB::connection('sqlsrv')->commit();
	Session::flash('msnExito', 'SE INGRESADO  LA SOLICITUD CORRECTAMENTE!');
	return redirect()->route('seguro');
	
		
	}
	
	public function storeLicencia(Request $request){
		//dd($request->all());
		 
		 if(!$request->has('concepto')){
            Session::flash('msnError', 'Error: Seleccione el concepto de la licencia!');
	       	return Redirect::back();
         }		 
	   DB::connection('sqlsrv')->beginTransaction();
	    try {
	    	
		 $licencia = new SolLicencia();
		 if($request->concepto==0){
			 $licencia->enConcepto=$request->catotros;
		 }else{
			$licencia->enConcepto=$request->concepto;
		 }
		 if($request->has('observacion')){
            $licencia->observaciones=$request->observacion;
         }
         else{
            $licencia->observaciones='';
         }
		 
		 if($request->goce==1){
			$licencia->goce=TRUE;
		 }
		 else if ($request->goce==0){
			 $licencia->goce=false;
		 }
		 
		 $fechaI = date('Y-m-d H:i:s.000',strtotime($request->fechaInicio." ".$request->fechaInicioH));
		 $fechaF = date('Y-m-d H:i:s.000',strtotime($request->fechaFin." ".$request->fechaFinH));
		 $licencia->dias=$request->dias;
		 $licencia->fechaInicio=$fechaI;		 
		 $licencia->fechaFin=$fechaF;
		 $licencia->idEmpleadoCrea=Auth::user()->idEmpleado;
		 $licencia->idUsuarioCrea=Auth::user()->idUsuario.'@'.$request->ip();
		 //$licencia->fechaCreacion=date('2017-01-13');
		 if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20){
		 	$licencia->idEstado=6;	
		 }
		 $licencia->save();
			 //dd($licencia);
		 $idSolicitud=$licencia->idSolLicencia;
		 $duiUser=Auth::user()->Empleado()->first()->dui;
		
		 if(empty($duiUser) || $duiUser==null){
			DB::connection('sqlsrv')->rollback();
		    Session::flash('msnError', 'Su informacion personal no esta completa, debe completarla para poder guardar sus solicitudes.');
			return redirect()->route('licencia');
		 }

		 $path='Y:\ECM\urh\eepl'.'\\'.$duiUser;
		 //$path='U:\Licencias';
		 $file= $request->file('file');
			//primero verifica si hay archivos a subir
			if(!empty($file)){
				$filesystem= new Filesystem();
		   		if($filesystem->exists($path)){
			    	if($filesystem->isWritable($path)){
						//si hay archivos crear la ruta con el id del usuario
						$carpeta=$path.'\\'.'Licencias';
						//crea la nueva carpeta
						File::makeDirectory($carpeta, 0777, true, true);
						// se guadarn en el disco 
						//dd($file->getClientOriginalName());
						//$name= Auth::user()->apellidosUsuario .$file1->getClientOriginalName();
						$name= 'Licencia#'.$idSolicitud.Auth::user()->idEmpleado.'.'.$file->getClientOriginalExtension();
						$type = $file->getMimeType();
						$file->move($carpeta,$name);
						//se enlanza cada archivo a su bitacora en la tabla ArchivoBitacora
						$archivo = new DocumentoSeguro();
						$archivo->urlDocumento=$carpeta.'\\'.$name;	
						$archivo->tipoDocumento=$type;
						$archivo->idSolSeguro=0;
						$archivo->idSolLicencia=$idSolicitud;
						$archivo->idUsuarioCrea= Auth::user()->idUsuario;
						$archivo->save();
					}
					else{
						Session::flash('msnError', 'PROBLEMAS INTERNOS CON EL DIRECTORIO!,NO SE HA PODIDO GUARDAR EL DOCUMENTO DE SU SOLICITUD, VUELVA A INGRESAR SU SOLICITUD!');
						return redirect()->route('licencia');
					}
				}
				else{
					Session::flash('msnError', 'PROBLEMAS INTERNOS CON EL SERVIDOR!, NO SE HA PODIDO ACCEDER A LA UNIDAD DE DISCO, VUELVA A INGRESAR SU SOLICITUD!');
					return redirect()->route('licencia');
				}
						
			}
			 //$this->sendEmail($request,$idSolicitud);
			 if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional!=20){
				$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
				->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
						from dnm_rrhh_si.RH.plazasFuncionales where 
						idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.Auth::user()->idEmpleado.'))'))
				->where('estadoId',1)
				->first();
				if(!empty($jefeInmediato)){
					$jefedata=User::where('idEmpleado',$jefeInmediato->idEmpleado)->first();
					$this->sendEmail($request,$idSolicitud,$jefedata);
				}else{
					Session::flash('msnError', '¡ESTIMADO USUARIO EXISTE UN PROBLEMA CON SU PLAZA FUNCIONAL, YA QUE NO POSEE NINGUN JEFE. NOTIFICAR INCONVENIENTE A LA UNIDAD DE RECURSOS HUMANOS!');
					return redirect()->route('licencia');
				}
			 	
			 }
			 
	   }
	   catch(Exception $e){
		    DB::connection('sqlsrv')->rollback();
		    throw $e;
		    Session::flash('msnError', $e->getMessage());
			return redirect()->route('licencia');
	   }
	   DB::connection('sqlsrv')->commit();
	   Session::flash('msnExito', 'SE INGRESADO  LA SOLICITUD CORRECTAMENTE!');
	   return redirect()->route('licencia');

		 //dd($concepto);
	}
	
	
	public function storeNoMarcacion(Request $request){
		//dd($request->all());
		//dd(date('H:i:s',strtotime($request->horaEntrada)));
		if(!$request->has('motivo')){
            Session::flash('msnError', 'Error: Seleccione el motivo!');
	       	return Redirect::back();
        }
		$respaldo=0;
		if ($request->motivo==23){
			
			if($request->dermatologico==1){
			$respaldo=1;
			}
			else if ($request->dermatologico==2){
				$respaldo=2;
			}
			
		}
		$noMarcacion = new SolNoMarcacion();
		$noMarcacion->motivo=$request->motivo;
		$noMarcacion->respaldo=$respaldo;
		$noMarcacion->fechaPermiso=$request->fechaSolicitud;
		$noMarcacion->horaEntrada=date('H:i:s',strtotime($request->horaEntrada));
		$noMarcacion->horaSalida=date('H:i:s',strtotime($request->horaSalida));
		$noMarcacion->observaciones=$request->observaciones;
		$noMarcacion->idEmpleadoCrea=Auth::user()->idEmpleado;
		$noMarcacion->idUsuarioCrea=Auth::user()->idUsuario.'@'.$request->ip();
		//$noMarcacion->fechaCreacion=date('Y-d-m H:i:s.000');
		//$noMarcacion->fechaCreacion=new Carbon\Carbon::createFromFormat('Y-d-m',date_create('Y-m-d'));
		$noMarcacion->save();
		//
		if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional!=20){
			$idSolicitud= $noMarcacion->idSolNoMarca;
			
			$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
							->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
									from dnm_rrhh_si.RH.plazasFuncionales where 
									idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.Auth::user()->idEmpleado.'))'))
							->where('estadoId',1)
							->first();
			if(empty($jefeInmediato)){
				 Session::flash('msnError', '¡ESTIMADO USUARIO EXISTE UN PROBLEMA CON SU PLAZA FUNCIONAL, YA QUE NO POSEE NINGUN JEFE. NOTIFICAR INCONVENIENTE A LA UNIDAD DE RECURSOS HUMANOS!');
				return redirect()->route('nomarcacion');
			}
			$data=[];
			$data['jefe']=User::where('idEmpleado',$jefeInmediato->idEmpleado)->first();
			$data['empleado']=CatEmpleados::find(Auth::user()->idEmpleado);
			$data['idTipo']=1;
			$data['tipo']='No Marcación';
			
			$solicitud=SolNoMarcacion::find($idSolicitud);
			$solmotv=SolicitudMotivo::find($solicitud->motivo);
			$motivo=CatMotivos::find($solmotv->idMotivo);
			$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
			$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
			//dd($motivo);
			$data['solicitud']=$solicitud;
			$data['empleado']=$empleado;
			$data['unidad']=$unidad;
			$data['motivo']=$motivo;
	        //$data['correo']=$correo;
			if(!empty($data['jefe']->correo)){
				Mail::send('emails.solicitudes',$data,function($msj) use ($data){
		            $msj->subject('Nueva Solicitud de No Marcacion');
					//
					//
					$msj->to($data['jefe']->correo);
		        });
			}
		}
		Session::flash('msnExito', 'SE INGRESADO  LA SOLICITUD CORRECTAMENTE!');
		return redirect()->route('nomarcacion');
	}
	
	public function getSolicitudes(){
		$data = ['title' 			=> 'Solicitudes'
				,'subtitle'			=> 'Licencias'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Solicitudes', 'url' => '#'],
			 		['nom'	=>	'Licencias', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;

		return view('solicitudes.permisos.solicitudes',$data);

	}

	public function getSolicitudesDNM(){
		$data = ['title' 			=> 'Solicitudes'
				,'subtitle'			=> 'Licencias'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Solicitudes', 'url' => '#'],
			 		['nom'	=>	'Licencias', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		$data['unidades']=Unidades::all();
		//return $data;
		return view('solicitudes.permisos.solicitudesDNM',$data);

	}

	
	public function getDataRowsSolicitudes(){
		
		$solicitudes=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwPermisosRrhh as permisos')
					->select(DB::raw('ROW_NUMBER() OVER(ORDER BY permisos.id ASC) AS Row#'),'permisos.*')
					->where('idEmpleadoCrea',Auth::user()->idEmpleado);
		
		//dd($solicitudes);
		return Datatables::of($solicitudes)
					->addColumn('estadoSol',function($dt){
						if($dt->idEstadoSol == 1)
							return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 2)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 3)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 4)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 5)
							return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 6)
							return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 7)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						

					})
					->addColumn('detalle', function ($dt) {
	            	 if($dt->idEstadoSol==1){
	            	 	/* '<a href="'.route('desistir.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->id]).'" class="btn btn-xs btn-danger btn-perspective" >DESISTIR<i class="fa fa-times-circle"></i></a>'.' '.*/
	                	 return	'<a href="#" onclick="confirmDesistir('.$dt->idTipo.','.$dt->id.');" class="btn btn-xs btn-danger btn-perspective" >DESISTIR<i class="fa fa-times-circle"></i></a>'.' '.'<a href="'.route('ver.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->id]).'" class="btn btn-xs btn-success btn-perspective" target="_blank"><i class="fa fa-search"></i></a>';
	             	 }
	             	 else{
	             	 	return	'<a href="'.route('ver.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->id]).'" class="btn btn-xs btn-success btn-perspective" target="_blank"><i class="fa fa-search"></i></a>';
	             	 }
	             
					})
					->make(true);	
		
	}

	/*
		DataTable de todas las solicitudes para vista de recursos humanos
	 */
	public function getDataRowSolicitudesAdmin(Request $request){
		
		$solicitudes=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwPermisosRrhh as permisos')
					->whereIn('idEstadoSol',[3,4,6]);
					#->select(DB::raw('ROW_NUMBER() OVER(ORDER BY permisos.id ASC) AS Row#'),'permisos.*')
					#->where('idUsuarioCrea',Auth::user()->idUsuario);
		
		//dd($request->all());
		

		return Datatables::of($solicitudes)
					->addColumn('estadoSol',function($dt){
						if($dt->idEstadoSol == 1)
							return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 2)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 3)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 4)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 5)
							return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 6)
							return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 7)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						

					})
					->addColumn('procesado', function ($dt) {
	            	    if($dt->procesada==0){
	                  		return '<button class="btn btn-danger btn-xs" id="NoProcesar" onclick="NoProcesar('.$dt->idTipo.','.$dt->id.',2)">
	                  	NO PROCESAR</button>'.' '.'<button class="btn btn-primary btn-xs" id="NoProcesar" onclick="NoProcesar('.$dt->idTipo.','.$dt->id.',1)">
	                  	PROCESAR <i class="fa fa-refresh" aria-hidden="true"></i></button>';
	             		}
	             		elseif($dt->procesada==1){
	             			return '<a href="" class="btn btn-xs btn-info btn-perspective">PROCESADA<i class="fa fa-check" aria-hidden="true"></i></a>';
	             		}
	             		elseif($dt->procesada==2){
	             			return '<a href="" class="btn btn-xs btn-danger btn-perspective">NO PROCESADA<i class="fa fa-times" aria-hidden="true"></i></a>';
	             		}
					})
					->addColumn('detalle', function ($dt) {
	            	    
	                 return	'<a href="'.route('ver.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->id]).'" class="btn btn-xs btn-success btn-perspective" target="_blank"><i class="fa fa-search"></i></a>';
	             
					})
					->filter(function($query) use ($request){
							
	        				if($request->has('unidad')){
	        					$query->where('idUnidad','=',$request->get('unidad'));
	        				}

	        				if($request->has('fechaInicio') and $request->has('fechaFin')){ 	
	        					$query->whereBetween(DB::raw('Convert(varchar(10), fechaCreacion,120)'),[date('Y-m-d',strtotime(str_replace("/","-",$request->get('fechaInicio')))),date('Y-m-d',strtotime(str_replace("/","-",$request->get('fechaFin'))))]);
	        				}
	        				elseif($request->has('fechaInicio')){ 	
	        					$query->where(DB::raw('Convert(varchar(10), fechaCreacion,120)'),'=',date('Y-m-d',strtotime(str_replace("/","-",$request->get('fechaInicio')))));
	        				}

	        				if($request->has('procesada')){
	        					$query->where('procesada','=',(int)$request->get('procesada'));
	        				}
	        				if($request->has('tipo')){
	        					$query->where('idTipo','=',(int)$request->get('tipo'));
	        				}


	        			})
					->make(true);	
		
	}
	
	public function getSolicitudesByUnidad(){
		$data = ['title' 			=> 'Permisos y Seguro'
				,'subtitle'			=> 'Solicitudes de la Unidad'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Solicitudes de la Unidad', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		//return $data;
		return view('solicitudes.permisos.solicitudesUnidad',$data);

	}
	
	public function getDataRowsSolicitudesByUnidad(){
		//$unidad=Unidades::getUnidadByIdEmpleado(Auth::user()->idEmpleado);
		$idPlazaFuncional=CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional;
		$empleados=CatEmpleados::getEmpleadosByIdPlazaPadre($idPlazaFuncional);
		//dd($empleados);
		
		$idEmpleados=[];
			if(count($empleados)>0){
				for($i=0;$i<count($empleados);$i++) {
					if($empleados[$i]->idEmpleado!=Auth::user()->idEmpleado){
						$idEmpleados[$i]=$empleados[$i]->idEmpleado;
					}
				}
			}
        $annio = Date('Y');
		$solicitudes=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.vwPermisosRrhh as permisos')
					->select(DB::raw('ROW_NUMBER() OVER(ORDER BY permisos.id ASC) AS Row#'),'permisos.*')
					->whereIn('idEmpleadoCrea',$idEmpleados)
                    ->whereRaw('YEAR(fechaCreacion) = '.$annio);
		
		//dd($solicitudes);
		return Datatables::of($solicitudes)
					->addColumn('estadoSol',function($dt){
						if($dt->idEstadoSol == 1)
							return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 2)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 3)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 4)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 5)
							return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 6)
							return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 7)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';

					})
					->addColumn('detalle', function ($dt) {
	                	return	'<a href="'.route('ver.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->id]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-search"></i></a>';
	             
					})
					->make(true);	
		
	}
	
	//public function get
	
	//funcion para enviar email de la solicitudes de licencias
	public function sendEmail($request,$idSolicitud,$jefeInmediato){
		//dd($request->all());
		$data=[];
		$data['jefe']=$jefeInmediato;
		$data['empleado']=CatEmpleados::find(Auth::user()->idEmpleado);
		$data['solicitud']='Licencia';
		//datos para el correo 
		$solicitud=SolLicencia::find($idSolicitud);
		//dd($solicitud);
		$solmotv=SolicitudMotivo::find($solicitud->enConcepto);
		//dd($solmotv);
		$motivo=CatMotivos::find($solmotv->idMotivo);
		//dd($motivo);
		$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
		$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
		//dd($motivo);
		$sol=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudLicencia')->where('idSolLicencia','=',$idSolicitud)->first();
		$data['solicitud']=$sol;
		$data['empleado']=$empleado;
		$data['unidad']=$unidad;
		$data['motivo']=$motivo;
		$data['idTipo']=2;
		$data['tipo']='Licencia';
		
		if($request->dias>90){
			//si es diferente a permiso de maternidad ya que este son maximo 90 dias
			if($request->concepto!=6){
				//doctor muelitas 
				$data['autorizada']=3;
				$correoSuperior='reina.acosta@medicamentos.gob.sv';
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to($correoSuperior);
				});
				
			}
			else{
				//si no es porque maternidad el permiso
				//jefe de unidad
				if(!empty($data['jefe']->correo)){
					Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);
					});
				}
			}
		}
		//caso especial
		elseif($request->catotros==30){
			//return 'correo a doctor coto';
			//doctor muelitas 
				$data['autorizada']=3;
				//dd($data);
				$correoSuperior='reina.acosta@medicamentos.gob.sv';
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to($correoSuperior);
				});
				//correo a jefe superior
		}
		//igual a 90 dias
		elseif($request->dias==90){
			//licencia personal
			if($request->concepto==4){
				//doctora muelitas 
				$data['autorizada']=2;
				$correoSuperior='jose.pena@medicamentos.gob.sv';
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to($correoSuperior);
				});
				//correo a jefe superior
				
				//jefe de unidad
				if(!empty($data['jefe']->correo)){
					Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);
					});
				}
			}
		}
		//igual a 90 dias
		elseif($request->dias==60){
			//licencia personal
			if($request->concepto==17){
				//doctora muelitas 
				$data['autorizada']=2;
				//$correoSuperior=
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to('jose.pena@medicamentos.gob.sv');
				});
				//correo a jefe superior
				
				//jefe de unidad
				if(!empty($data['jefe']->correo)){
					Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);
					});
				}
			}
		}
		//tramite de docente enseñanza superior
		elseif($request->concepto==31){
			//doctora muelitas 
				$data['autorizada']=2;
				
				//$correoSuperior=
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to('jose.pena@medicamentos.gob.sv');
				});
				//correo a jefe superior
				
				//jefe de unidad
				if(!empty($data['jefe']->correo)){
					Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);
					});
				}
				
		}
		//permiso por estudio
		elseif($request->concepto==12){
			//doctora muelitas 
				$data['autorizada']=2;
				//$correoSuperior=
				Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
				$msj->subject('Nueva Solicitud de Licencia');
				$msj->to('jose.pena@medicamentos.gob.sv');
				});
				//correo a jefe superior
				
				//jefe de unidad
				if(!empty($data['jefe']->correo)){
					Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);
					});
				}
				
		}
		// otro caso que no este contemplado
		else{
			//dd($data);
			//'steven.mena@medicamentos.gob.sv'
			if(!empty($data['jefe']->correo)){
				Mail::send('emails.solicitudes',$data,function($msj) use ($data){
					$msj->subject('Nueva Solicitud de Licencia');
					$msj->to($data['jefe']->correo);

				});
			}
		}
	}
	
	public function verificarDiasPermiso(Request $request){
		//dd($request->all());
		$endDate = strtotime($request->get('fechaFin'));
		$startDate = strtotime($request->get('fechaInicio'));
		$dias=InicioController::getWorkingDays($endDate,$startDate);
		$empleado=CatEmpleados::find(Auth::user()->idEmpleado);
		$categoria=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudMotivo as solm')
					->join('dnm_rrhh_si.Permisos.categoriaMotivos as catmo','solm.idMotivo','=','catmo.idMotivo')
					->select('catmo.*')
					->where('solm.idSolMot',$request->concepto)->get();
		if(count($categoria)==0){
			return $dias;
		}
		if(count($categoria)>1){
			if(in_array($empleado->sexo,json_decode($categoria[0]->sexo))){
				$categoria=$categoria[0];
			}
			elseif(in_array($empleado->sexo,json_decode($categoria[1]->sexo))){
				$categoria=$categoria[1];
			}
		}
		else{
			//$genero=json_decode($categoria[0]->sexo);
			$categoria=$categoria[0];
		}

		
		if($dias->getData()->status==200){
			$sumdias=DB::connection('sqlsrv')->table('dnm_rrhh_si.Permisos.solicitudLicencia as solm')
					->where('solm.enConcepto','=',$request->concepto)
					->where(DB::raw('YEAR(fechaCreacion)'),date('Y'))
					->where('solm.idEmpleadoCrea',Auth::user()->idEmpleado)
					->whereIn('solm.idEstado',[3,4])
					->select(DB::raw('SUM(solm.dias) as suma'))
					->first()->suma;
		 
			if(in_array($empleado->sexo,json_decode($categoria->sexo))){
				if($request->concepto==10){
			
					//dd($sumdias);
					//verificamos si nunca ha utilizado este permiso
					if($sumdias==0){
						//si es cierto comparamos que los dias solicitados
						//no sean mayor a los dias maximo
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							return response()->json(['status' => 400,'message' => "La mayor cantidad para matrimonio son 8 dias."]);	
						}
					}
					//verificamos si los dias de las solicitudes de matrimonio son menores a los dias maximo
					//si son menores puede ingresar otra solicitu de esta hasta completar los 8 
					
					if($sumdias<$categoria->diasmax){
						//dd($categoria->diasmax);
						$diasrestante=(int)$categoria->diasmax-$sumdias;
						//dd($dias);
						if($dias->getData()->data<=$diasrestante){
							//dd('entro');
							return $dias;
						}
						else{
							return response()->json(['status' => 400,'message' => "La mayor cantidad para matrimonio son 8 dias, y Ud. ya ha utilizado ".$sumdias.", su solicitud no procede."]);	
						}
					}
					elseif($categoria->diasmax==$sumdias){
						return response()->json(['status' => 400,'message' => "Ud. ya ha utilizado los 8 dias año calendario para Matrimonio, su solicitud no procede."]);	
					}
				}
				
				//alumbramiento
				if($request->concepto==16){
					if($empleado->sexo==="M"){
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							return response()->json(['status' => 400,'message' => "La mayor cantidad para alumbramiento son 3 dias."]);	
						}
					}
					
					if($empleado->sexo==="F"){
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							return response()->json(['status' => 400,'message' => "La mayor cantidad para alumbramiento son 30 dias."]);	
						}
					}
				}
				//adopcion
				if($request->concepto==17){
					if($empleado->sexo==="M"){
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							return response()->json(['status' => 400,'message' => "La mayor cantidad para adopcion son 3 dias."]);	
						}
					}
					
					if($empleado->sexo==="F"){
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							return response()->json(['status' => 400,'message' => "La mayor cantidad para adopcion son 60 dias."]);	
						}
					}
				}
				//maternidad
				if($request->concepto==6){
					
					if($dias->getData()->data<=$categoria->diasmax)
						return $dias;
					elseif($dias->getData()->data > $categoria->diasmax)
						return response()->json(['status' => 400,'message' => "La mayor cantidad para maternidad son 120 dias."]);	
				}
					
				
				
				if($request->concepto==4){
					//dd($sumdias);
					//dd($request->all());
					//verificamos si nunca ha utilizado este permiso
					if($sumdias==0){
						//si es cierto comparamos que los dias solicitados
						//no sean mayor a los dias maximo
						if($dias->getData()->data<=$categoria->diasmax)
							return $dias;
						elseif($dias->getData()->data > $categoria->diasmax){
							
							return response()->json(['status' => 200,'message' => "Recuerde que la maxima cantidad de dias con goce son 5 dias año calendario."]);	
							
						}
					}
					//verificamos si los dias de las solicitudes de permiso personal son menores a los dias maximo
					//si son menores puede ingresar otra solicitu de esta hasta completar los 5
					
					if($sumdias<$categoria->diasmax){
						//dd($categoria->diasmax);
						$diasrestante=(int)$categoria->diasmax-$sumdias;
						//dd($dias);
						if($dias->getData()->data<=$diasrestante){
							//dd('entro');
							return $dias;
						}
						else{
							if($request->goce==1){
							 return response()->json(['status' => 200,'message' => "La mayor cantidad para permiso personal son 5 dias, y Ud. ya ha utilizado ".$sumdias.", ."]);	
							}
							else{
								return $dias;
							}
						}
					}
					elseif($categoria->diasmax==$sumdias){
						if($request->goce==1){
							return response()->json(['status' => 400,'message' => "Ud. ya ha utilizado los 5 dias año calendario para permiso personal, debe seleccionar sin goce de salario para que su solicitud sea ingresada."]);	
						}
						else{
							return $dias;
						}
					}
				}
			}
			else{
				return response()->json(['status' => 400,'message' => "Este tipo de permiso no aplica para su genero."]);
			}		
			
			if($dias->getData()->data>90){
				if($request->concepto!=12){
					if($request->goce==1){
						return response()->json(['status' => 400,'message' => "Mayor a 90 dias no puede seleccionar con goce de sueldo la licencia."]);
					}
				}
			}
			//dd(json_decode(trim($categoria->sexo)));
			/*
			
			//dd($categoria);*/
			return $dias;	
			
		}
		elseif($dias->getData()->status==400){
			return $dias;
		}
	}
	
	public function denegar(Request $request){
		//dd($request->all());
		if($request->idTipo==1){
			$solnomarcacion=SolNoMarcacion::find($request->idSolicitud);
			$solnomarcacion->autorizacion1=Auth::user()->idEmpleado;
			$solnomarcacion->idEstado=2;
			$solnomarcacion->fechaApruebaDenegar = date('Y-m-d H:i:s');
			//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
			$solnomarcacion->save();
			return response()->json(['status' => 200,'message' => "La solicitud ha sido denegada"]);	
		}
		elseif($request->idTipo==2){
			$sollicencia=SolLicencia::find($request->idSolicitud);
			$sollicencia->autorizacion1=Auth::user()->idEmpleado;
			$sollicencia->idEstado=2;
			$sollicencia->fechaApruebaDenegar = date('Y-m-d H:i:s');
			//$solnomarcacion->fechaModificacion=date('Y-m-d H:i:s.000');
			$sollicencia->save();
			return response()->json(['status' => 200,'message' => "La solicitud ha sido denegada"]);	
		}
		
	}
	
	public function solicitudesSeguro(){
		$data = ['title' 			=> 'Permisos y Seguro'
				,'subtitle'			=> 'Solicitudes de Seguro'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Solicitudes de seguro', 'url' => '#']
				]]; 

		
		return view('solicitudes.permisos.solicitudesSeguro',$data);

	}
	
	public function solicitudesLicenciaDirector(){
		
		$data = ['title' 			=> 'Permisos y Seguro'
				,'subtitle'			=> 'Listado de licencias'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Permisos y Seguro', 'url' => '#'],
			 		['nom'	=>	'Licencias a Autorizar', 'url' => '#']
				]]; 
		//*/
		
		return view('solicitudes.permisos.licenciasAutorizar',$data);
	}

	public function getDataRowsLicenciaDirector(){
		if(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==20){
			$licencias=SolLicencia::getLicenciaDirector();	
		}
		elseif(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==19){
			$licencias=SolLicencia::getLicenciasNivel2();
		}
		elseif(CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional==25){
			$licencias=VwAllPermisos::where('idPlazaFuncionalPadre',25)->get();
		}
		return Datatables::of($licencias)
				->addColumn('tipo', function ($dt) {
							if($dt->idTipo==1){
								return	'NO MARCACIÓN';	
							}
							elseif($dt->idTipo==2){
								return	'LICENCIA';	
							}
						})
				->addColumn('estadoSol',function($dt){
						if($dt->idEstadoSol == 1)
							return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 2)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 3)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 4)
							return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 5)
							return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 6)
							return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
						else if($dt->idEstadoSol == 7)
							return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';

					})
					->addColumn('detalle', function ($dt) {
	                	return	'<a href="'.route('ver.solicitud',['idTipo' =>$dt->idTipo,'idSolicitud' => $dt->idSolLicencia]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-search"></i></a>';
	             
					})
				->make(true);
	}


	public function getDataRowsSeguros(){
		$seguros=SolSeguro::getSolicitudesSeguro();
		return Datatables::of($seguros)
			->addColumn('detalle', function ($dt) {
							return	'<a href="'.route('ver.seguro',['idSolSeguro' =>Crypt::encrypt($dt->idSolSeguro)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-search"></i></a>';
					 			
						})
			->make(true);
	
	}
	
	
	//funcion para descargar los documentos 
	public function download($idDocumento)
	{
	      //dd($tipoDocumento);

	     if($idDocumento!=""){

			$idDocumento=Crypt::decrypt($idDocumento);   
			$documento=DocumentoSeguro::where('idDocumento',$idDocumento)->first();
	        //$urlDocumento=substr_replace($documento->urlDocumento,"V",0,1);
	        $urlDocumento=$documento->urlDocumento;
			$tipoArchivo=trim($documento->tipoDocumento);
			
			if($tipoArchivo=='application/pdf'){		
									if (File::isFile($urlDocumento))
									{

										try {
												    $file = File::get($urlDocumento);
												    $response = Response::make($file, 200);			    
												    $response->header('Content-Type', 'application/pdf');

												    return $response;
									     } catch (Exception $e) {
	                                            
											    return Response::download(trim($urlDocumento));
									     }
									}else{
										return back();
									}
			}else if($tipoArchivo=='image/png' or $tipoArchivo==='image/jpeg'){
										if (File::isFile($urlDocumento))
										{
										
										    $file = File::get($urlDocumento);
										    $response = Response::make($file, 200);
										    // using this will allow you to do some checks on it (if pdf/docx/doc/xls/xlsx)
										     $content_types = [
							                'image/png', // png etc
							                'image/jpeg', // jpeg
							                  ];
										    $response->header('Content-Type', $content_types);

										    return $response;
										}else{
											//REToRNA A LA VISTA SI NO EXISTE ESE ARCHIVO
										return back();
									    }
				}else{

								if (File::isFile($urlDocumento))
								{	
						
								    return Response::download(trim($urlDocumento));
								}
			}

		}
	}


	public function procesarRrhh(Request $request){
		//dd($request->all());

		//si es tipo 1, SolNoMarcacion
		if($request->idTipo==1){
			$solnomarcacion=SolNoMarcacion::find($request->idSolicitud);
			$solnomarcacion->procesada=$request->accion;
			if($solnomarcacion->save()){
				return response()->json(['status' => 200, 'message' => "Su solicitud ha sido procesada"]);	
			}
			else{
				return response()->json(['status' => 400, 'message' => "No se ha podido procesar la solicitud"]);		
			}
			
		}
		//si es tipo 2, SolLicencia
		elseif($request->idTipo==2){
			$sollicencia=SolLicencia::find($request->idSolicitud);
			$sollicencia->procesada=$request->accion;
			if($sollicencia->save()){
				return response()->json(['status' => 200, 'message' => "Su solicitud ha sido procesada"]);	
			}
			else{
				return response()->json(['status' => 400, 'message' => "No se ha podido procesar la solicitud"]);
			}
			
		}
		
	}

	//$idTipo,$idSolicitud
	public function changeEstadoSol(Request $request){
		//dd($request->all());
		if($request->idTipo==1){
			$solnomarcacion=SolNoMarcacion::find($request->idSolicitud);
			$solnomarcacion->idEstado=7;
			if($solnomarcacion->save()){
				return response()->json(['status' => 200, 'message' => "Su solicitud ha sido desistida"]);
			}
		}
		//si es tipo 1, SolLicencia
		elseif($request->idTipo==2){
			$sollicencia=SolLicencia::find($request->idSolicitud);
			$sollicencia->idEstado=7;
			if($sollicencia->save()){
				return response()->json(['status' => 200, 'message' => "Su solicitud ha sido desistida"]);
			}
		}
	}

	public function resendEmailAuth(Request $request)
	{
		try {
			if ($request->idSol) 
			{
				switch ($request->tipo) {
					case 1: #Solicitud de No Marcacion
							$solicitud = SolNoMarcacion::find($request->idSol);
							if($solicitud)
							{
								$idSolicitud= $solicitud->idSolNoMarca;

								$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
												->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
														from dnm_rrhh_si.RH.plazasFuncionales where 
														idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.$solicitud->idEmpleadoCrea.'))'))
												->where('estadoId',1)
												->first();
								$data=[];
								$data['jefe']=User::where('idEmpleado',$jefeInmediato->idEmpleado)->first();
								$data['empleado']=CatEmpleados::find(Auth::user()->idEmpleado);
								$data['idTipo']=1;
								$data['tipo']='No Marcación';

								$solicitud=SolNoMarcacion::find($idSolicitud);
								$solmotv=SolicitudMotivo::find($solicitud->motivo);
								$motivo=CatMotivos::find($solmotv->idMotivo);
								$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
								$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
								//dd($motivo);
								$data['solicitud']=$solicitud;
								$data['empleado']=$empleado;
								$data['unidad']=$unidad;
								$data['motivo']=$motivo;
								//$data['correo']=$correo;
								if(!empty($data['jefe']->correo))
								{
									Mail::send('emails.solicitudes',$data,function($msj) use ($data){
								        $msj->subject('Nueva Solicitud de No Marcacion');
										$msj->to($data['jefe']->correo);
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
								    });
								    echo 'Email Sent to '.$data['jefe']->correo;
								}							
								else
								{
									echo 'Email not sent';
								}
							}
							
						break;
					case 2: #Solicitudes de licendia
							$solicitud=SolLicencia::find($request->idSol);
							if($solicitud)
							{	
								$jefeInmediato=DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
									->where('idPlazaFuncional',DB::raw('(select idPlazaFuncionalPadre 
											from dnm_rrhh_si.RH.plazasFuncionales where 
											idPlazaFuncional=(select idPlazaFuncional from dnm_rrhh_si.RH.empleados where idEmpleado='.$solicitud->idEmpleadoCrea.'))'))
									->where('estadoId',1)
									->first();

								$data=[];
								$data['jefe']=User::where('idEmpleado',$jefeInmediato->idEmpleado)->first();
								$data['empleado']=CatEmpleados::find($solicitud->idEmpleadoCrea);
								$data['solicitud']='Licencia';
								//datos para el correo 
								
								//dd($solicitud);
								$solmotv=SolicitudMotivo::find($solicitud->enConcepto);
								//dd($solmotv);
								$motivo=CatMotivos::find($solmotv->idMotivo);
								//dd($motivo);
								$empleado=CatEmpleados::find($solicitud->idEmpleadoCrea);
								$unidad=Unidades::getUnidadByIdEmpleado($empleado->idEmpleado);
								//dd($motivo);
								$data['solicitud']=$solicitud;
								$data['empleado']=$empleado;
								$data['unidad']=$unidad;
								$data['motivo']=$motivo;
								$data['idTipo']=2;
								$data['tipo']='Licencia';
								
								if($solicitud->dias>90){
									//si es diferente a permiso de maternidad ya que este son maximo 90 dias
									if($solicitud->concepto!=6){
										//doctor muelitas 
										$data['autorizada']=3;
										$correoSuperior='reina.acosta@medicamentos.gob.sv';
										Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
										$msj->subject('Nueva Solicitud de Licencia');
										$msj->to($correoSuperior);
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});
										
									}
									else{
										//si no es porque maternidad el permiso
										//jefe de unidad
										if(!empty($data['jefe']->correo)){
											Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
											});
										}
									}
								}
								elseif($solicitud->dias==90){
									//licencia personal
									if($solicitud->concepto==4){
										//doctora muelitas 
										$data['autorizada']=2;
										$correoSuperior='jose.pena@medicamentos.gob.sv';
										Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
										$msj->subject('Nueva Solicitud de Licencia');
										$msj->to($correoSuperior);
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});
										//correo a jefe superior
										
										//jefe de unidad
										if(!empty($data['jefe']->correo)){
											Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
											});
										}
									}
								}
								//igual a 90 dias
								elseif($solicitud->dias==60){
									//licencia personal
									if($solicitud->concepto==17){
										//doctora muelitas 
										$data['autorizada']=2;
										//$correoSuperior=
										Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
										$msj->subject('Nueva Solicitud de Licencia');
										$msj->to('jose.pena@medicamentos.gob.sv');
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});
										//correo a jefe superior
										
										//jefe de unidad
										if(!empty($data['jefe']->correo)){
											Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
											});
										}
									}
								}
								//tramite de docente enseñanza superior
								elseif($solicitud->concepto==31){
									//doctora muelitas 
										$data['autorizada']=2;
										
										//$correoSuperior=
										Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
										$msj->subject('Nueva Solicitud de Licencia');
										$msj->to('jose.pena@medicamentos.gob.sv');
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});
										//correo a jefe superior
										
										//jefe de unidad
										if(!empty($data['jefe']->correo)){
											Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
											});
										}
										
								}
								//permiso por estudio
								elseif($solicitud->concepto==12){
									//doctora muelitas 
										$data['autorizada']=2;
										//$correoSuperior=
										Mail::send('emails.solicitudaut2',$data,function($msj) use ($correoSuperior){
										$msj->subject('Nueva Solicitud de Licencia');
										$msj->to('jose.pena@medicamentos.gob.sv');
										$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});

										//correo a jefe superior
										
										//jefe de unidad
										if(!empty($data['jefe']->correo)){
											Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
											});
											echo 'Email sent to '.$data['jefe']->correo;
										}
										
								}
								// otro caso que no este contemplado
								else{

									if(!empty($data['jefe']->correo)){
										Mail::send('emails.solicitudes',$data,function($msj) use ($data){
											$msj->subject('Nueva Solicitud de Licencia');
											$msj->to($data['jefe']->correo);
											$msj->bcc('solicitudes.administrativas@medicamentos.gob.sv');
										});
										echo 'Email sent to '.$data['jefe']->correo;
									}
									else
									{
										echo 'No Email config';
									}
								}
							}
						break;
				}
			}
			else
			{
				return null;
			}
		} catch (\Exception $e) {
			dd($e);
		}			

	}
}
