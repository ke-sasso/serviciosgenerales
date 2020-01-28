<?php namespace App\Http\Controllers;

use App\Http\Requests\TrpSoliRequest;
use App\Http\Controllers\Controller;
use App\Trp_Solicitud;
use App\Cat_EstadoS;
use App\CatEmpleados;
use Auth;
use DB;
use Crypt;
use App\User;
use Session;
use App\CatVehiculo;
use App\CatMotorista;
use App\DetalleVehiculo;
use App\Detalle_Personas;
use App\CatJefes;
use App\Unidades;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;

class TransporteController extends Controller {

	/*	public function getIndex()
{
    return view('datatables.index');
}*/
	public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
		$data = ['title' 			=> 'Mision Oficial y Transporte'
				,'subtitle'			=> 'Administrador Solicitudes'
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Administrador solicitudes', 'url' => '#'],
			 		['nom'	=>	'Transporte', 'url' => '#']
				]]; 

		$data['usuario']= Auth::user()->idUsuario;
		//return $data;
		return view('solicitudes.establecimientos.admin',$data);

	}
	
		public function solicitudesUnidad(){
			$data = ['title' 			=> 'Mision Oficial y Transporte'
					,'subtitle'			=> 'Solicitudes de la Unidad'
					,'breadcrumb' 		=> [
						['nom'	=>	'Administrador solicitudes', 'url' => '#'],
						['nom'	=>	'Solicitudes de Transporte', 'url' => '#']
					]]; 

			$data['usuario']= Auth::user()->idUsuario;
			//return $data;
			return view('solicitudes.establecimientos.solicitudesUnidad',$data);

		}

		
		public function getNuevaSolicitud(){
			 $data = ['title' 			=> 'Mision Oficial y Transporte:'
					,'subtitle'			=> 'Nueva Solicitud'
					,'breadcrumb' 		=> [
			 		['nom'	=>	'Administrador solicitudes', 'url' => route('solicitudes.est')],
			 		['nom'	=>	'Transporte', 'url' => '#']
				]]; 

			$empleados=CatEmpleados::getEmpleadosByUnidad(17);
			
			//dd($empleados);
			$data['empleados']=$empleados;

			return view ('solicitudes.establecimientos.nuevasolicitud',$data);
		}

		/**
 * Process datatables ajax request.
 *
 * @return \Illuminate\Http\JsonResponse
 */   // informacion del DataTable que se presentara para el admin y para otra usuario y ser mostrada en admin.b
		public function anyData()
		{	
			$this->changeAllEstados();
			if(Auth::user()->idUsuario ==='admin')
			{
				return Datatables::of(DB::table('trp_solicitud')
				->join('cat_estado_solicitud','trp_solicitud.idEstado','=','cat_estado_solicitud.idEstado')
				->leftJoin('detalle_personas','trp_solicitud.idSolicitud','=','detalle_personas.idSolicitud')
				->join('dnm_catalogos.sys_usuarios as usuario','trp_solicitud.idUsuarioCrea','=','usuario.idUsuario')
				->select('trp_solicitud.idSolicitud','trp_solicitud.fechaCreacion','trp_solicitud.fechaTransporte','trp_solicitud.horaInicio','trp_solicitud.horaFin',
						  'trp_solicitud.lugar','trp_solicitud.descripcion',DB::raw('concat(nombresUsuario,'.'" "'.',apellidosUsuario) as nombresUsuario'),'cat_estado_solicitud.idEstado','cat_estado_solicitud.nombreEstado')
				->whereNotIn('trp_solicitud.idEstado',[1])
				->groupBy('trp_solicitud.idSolicitud'))
				//select concat(cat_motorista.nombre,' ',cat_motorista.apellido) as motorista,numPlaca,marca,modelo,año 
				//from cat_vehiculos,detalle_vehiculo,cat_motorista where 
				//cat_vehiculos.idVehiculo=detalle_vehiculo.idVehiculo and cat_vehiculos.idMotorista=cat_motorista.idMotorista and detalle_vehiculo.idSolicitud=28
				->addColumn('estado',function($dt){
        		if($dt->idEstado == 9)
        			return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 2)
        			return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 3)
        			return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 4 || $dt->idEstado == 5)
        			return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 6 || $dt->idEstado == 7)
        			return '<span class="label label-warning" >'.$dt->nombreEstado.'</span>';
        		else
        			return '<span class="label label-success">'.$dt->nombreEstado.'</span>';

        	})
				->addColumn('Persona',function($dt){
					
					
					/*$sql="select group_concat(concat(catep.nombres,' ',catep.apellidos)) as nombres
						from dnm_catalogos.cat_empleados_dnm  as catep
						where catep.idEmpleado IN (select dp.idEmpleado from detalle_personas as dp where dp.idSolicitud=".$dt->idSolicitud.")";*/
					$idEmpleados= DB::table('dnm_sol_admin_si.detalle_personas')
									->select('idEmpleado')->where('idSolicitud',$dt->idSolicitud)->get();
					$emp=[];
						if(count($idEmpleados)>0){
							for($i=0;$i<count($idEmpleados);$i++) {
								$emp[$i]=$idEmpleados[$i]->idEmpleado;
							}
						}
					$personas=$this->concatPersonas($emp);
					//dd($idEmpleados);
					//$personas = DB::select(DB::raw($sql));
						//return $motorista[0]->motorista;
						if($personas==null){
							return 'No se le ha sido asignado ningun motorista.';
							
							
						}
							
					
						else{
							return $personas;
							
						}
					
				})	
				->addColumn('vehiculo',function($dt){
					
						$vehiculos = DB::table('cat_vehiculos as cv')->join('detalle_vehiculo as dv','cv.idVehiculo','=','dv.idVehiculo')
						 		 		->where('idSolicitud',$dt->idSolicitud)
						 				->select(DB::raw('concat('.'"#PLACA:"'.',numPlaca,'.'", "'.',"MARCA:"'.',marca,'.'", "'.',"TIPO VEHICULO:"'.',tipo) as vehiculo'))
						 				->get();
						 				//DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista')
						 //return $vehiculos[0]->vehiculo;
						if($vehiculos==null){
							
							return 'No se le ha sido asignado ningun vehiculo.';
							
						}
							
					
						else{
							
							return $vehiculos[0]->vehiculo;
						}
					
				})
				->addColumn('motorista', function($dt){
			 		
						$sql = "select concat(nombre,' ',apellido) as motorista from cat_motorista where idMotorista=(select idMotorista from detalle_vehiculo 
						where idSolicitud=".$dt->idSolicitud.")";
						$motorista = DB::select(DB::raw($sql));
						//return $motorista[0]->motorista;
						if($motorista==null){
							return 'No se le ha sido asignado ningun motorista.';
							
							
						}
							
					
						else{
							return $motorista[0]->motorista;
							
						}
					
			 	})
				->addColumn('asignacion', function ($dt) {
	            	if($dt->nombreEstado ==="AUTORIZADA" ||  $dt->nombreEstado ==="REPROGRAMADA" ){
	                	return '<a href="'.route('transporte.asignarvehiculo',['idEstado' =>2,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-primary btn-perspective"><i class="fa fa-pencil-square-o"></i> Asignar</a>'.' '.
	                			'<a href="'.route('transporte.cambiarestado',['idEstado' =>5,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-danger btn-perspective"><i class="fa fa-pencil-square-o"></i> Cancelar </a>';
					}
					else if ($dt->nombreEstado === "ASIGNADA")  {              			
	                	return	'<a href="'.route('transporte.cambiarestado',['idEstado' =>3,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-info btn-perspective"><i class="fa fa-pencil-square-o"></i> En Transito</a>'.' '.
	                			'<a href="'.route('transporte.cambiarestado',['idEstado' =>5,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-danger btn-perspective"><i class="fa fa-pencil-square-o"></i> Cancelar </a>';
	                	}
	                else if($dt->idEstado === 3) {              			
	                	return	'<a href="'.route('transporte.cambiarestado',['idEstado' =>7,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-pencil-square-o"></i> Pendiente </a>';
	                			
	                	}
	                 else if($dt->idEstado === 7) {              			
	                	return	'<a href="'.route('transporte.cambiarestado',['idEstado' =>8,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-pencil-square-o"></i> Realizada </a>';
	                			
	                	}
					else if ($dt->idEstado === 8) {              			
	                	return	'<a href="'.route('transporte.asignarkms',['idEstado' =>8,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-pencil-square-o"></i> Ingresar Kms </a>';
	                			
	                	}
	             
	            })
				->make(true);

			}
			//ver las solicitudes de la unidad 
			elseif(CatJefes::where('idPlazaFuncional',CatEmpleados::find(Auth::user()->idEmpleado)->idPlazaFuncional)->first()!=null){
				$unidad=Unidades::getUnidadByIdEmpleado(Auth::user()->idEmpleado);
				$empleados=CatEmpleados::getEmpleadosByUnidad($unidad->idUnidad);
				
				$idEmpleados=[];
				if(count($empleados)>0){
					for($i=0;$i<count($empleados);$i++) {
						$idEmpleados[$i]=$empleados[$i]->idEmpleado;
					}
				
				}
				return Datatables::of(DB::table('trp_solicitud')
				->join('cat_estado_solicitud','trp_solicitud.idEstado','=','cat_estado_solicitud.idEstado')
				->leftJoin('detalle_personas','trp_solicitud.idSolicitud','=','detalle_personas.idSolicitud')
				->join('dnm_catalogos.sys_usuarios as usuario','trp_solicitud.idUsuarioCrea','=','usuario.idUsuario')
				->select('trp_solicitud.idSolicitud','trp_solicitud.fechaCreacion','trp_solicitud.fechaTransporte','trp_solicitud.horaInicio','trp_solicitud.horaFin',
						  'trp_solicitud.lugar','trp_solicitud.descripcion',DB::raw('concat(nombresUsuario,'.'" "'.',apellidosUsuario) as nombresUsuario'),'cat_estado_solicitud.idEstado','cat_estado_solicitud.nombreEstado')
				->whereIn('trp_solicitud.idEmpleadoCrea',$idEmpleados)
				->groupBy('trp_solicitud.idSolicitud')) 
				 ->addColumn('estado',function($dt){
					if($dt->idEstado == 1)
						return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
					else if($dt->idEstado == 2)
						return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
					else if($dt->idEstado == 3)
						return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
					else if($dt->idEstado == 4 || $dt->idEstado == 5)
						return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
					else if($dt->idEstado == 6 || $dt->idEstado == 7)
						return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
					else
						return '<span class="label label-success">'.$dt->nombreEstado.'</span>';

				})
				 ->addColumn('Persona',function($dt){

						$idEmpleados= DB::table('dnm_sol_admin_si.detalle_personas')
										->select('idEmpleado')->where('idSolicitud',$dt->idSolicitud)->get();
						
						$emp=[];
							if(count($idEmpleados)>0){
								for($i=0;$i<count($idEmpleados);$i++) {
									$emp[$i]=$idEmpleados[$i]->idEmpleado;
								}
							}
						$personas=$this->concatPersonas($emp);
							if($personas==null){
								return 'No se le ha sido asignado ningun motorista.';
								
							}
							else{
								return $personas;
							}
					})
				->addColumn('vehiculo',function($dt){
						
							 $vehiculos = DB::table('cat_vehiculos as cv')->join('detalle_vehiculo as dv','cv.idVehiculo','=','dv.idVehiculo')
											->where('idSolicitud',$dt->idSolicitud)
											->select(DB::raw('concat('.'"#PLACA:"'.',numPlaca,'.'", "'.',"MARCA:"'.',marca,'.'", "'.',"TIPO VEHICULO:"'.',tipo) as vehiculo'))
											->get();
											
							if($vehiculos==null){
								
								return 'No se le ha sido asignado ningun vehiculo.';
							}
								
						
							else{
								return $vehiculos[0]->vehiculo;
								
							}
						
					})
				 ->addColumn('motorista', function($dt){
						
						$sql = "select concat(nombre,' ',apellido) as motorista from cat_motorista where idMotorista=(select idMotorista from detalle_vehiculo 
								where idSolicitud=".$dt->idSolicitud.")";
						$motorista = DB::select(DB::raw($sql));
						if($motorista==null){
								
								return 'No se le ha sido asignado ningun motorista.';
							}
								
						
							else{
								return $motorista[0]->motorista;
								
							}
						
					})
				 ->addColumn('asignacion', function ($dt) {
					if($dt->idEstado == 1 ||  $dt->nombreEstado ==="REPROGRAMADA"){
						return '<a href="'.route('transporte.cambiarestado',['idEstado' =>9,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-success btn-perspective"><i class="fa fa-pencil-square-o"></i> AUTORIZAR</a>'.
						'<a href="'.route('transporte.cambiarestado',['idEstado' =>5,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-danger btn-perspective"><i class="fa fa-pencil-square-o"></i> Cancelar </a>';;
					} 
					

				})
				->make(true);
				
			}
			else{
			 return Datatables::of(DB::table('trp_solicitud')
			->join('cat_estado_solicitud','trp_solicitud.idEstado','=','cat_estado_solicitud.idEstado')
			->leftJoin('detalle_personas','trp_solicitud.idSolicitud','=','detalle_personas.idSolicitud')
			->join('dnm_catalogos.sys_usuarios as usuario','trp_solicitud.idUsuarioCrea','=','usuario.idUsuario')
			->select('trp_solicitud.idSolicitud','trp_solicitud.fechaCreacion','trp_solicitud.fechaTransporte','trp_solicitud.horaInicio','trp_solicitud.horaFin',
					  'trp_solicitud.lugar','trp_solicitud.descripcion',DB::raw('concat(nombresUsuario,'.'" "'.',apellidosUsuario) as nombresUsuario'),'cat_estado_solicitud.idEstado','cat_estado_solicitud.nombreEstado')
			->where('trp_solicitud.idUsuarioCrea',Auth::user()->idUsuario)
			->groupBy('trp_solicitud.idSolicitud')) 
			 ->addColumn('estado',function($dt){
        		if($dt->idEstado == 1)
        			return '<span class="label label-info">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 2)
        			return '<span class="label label-primary">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 3)
        			return '<span class="label label-success">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 4 || $dt->idEstado == 5)
        			return '<span class="label label-danger">'.$dt->nombreEstado.'</span>';
        		else if($dt->idEstado == 6 || $dt->idEstado == 7)
        			return '<span class="label label-warning">'.$dt->nombreEstado.'</span>';
        		else
        			return '<span class="label label-success">'.$dt->nombreEstado.'</span>';

        	})
			 ->addColumn('Persona',function($dt){

					/*$sql="select group_concat(concat(catep.nombres,' ',catep.apellidos)) as nombres
						from dnm_catalogos.cat_empleados_dnm  as catep
						where catep.idEmpleado IN (select dp.idEmpleado from detalle_personas as dp where dp.idSolicitud=".$dt->idSolicitud.")";*/
					$idEmpleados= DB::table('dnm_sol_admin_si.detalle_personas')
									->select('idEmpleado')->where('idSolicitud',$dt->idSolicitud)->get();
					
					$emp=[];
						if(count($idEmpleados)>0){
							for($i=0;$i<count($idEmpleados);$i++) {
								$emp[$i]=$idEmpleados[$i]->idEmpleado;
							}
						}
					$personas=$this->concatPersonas($emp);
					//dd($emp);
					//$personas = DB::select(DB::raw($sql));
						//return $motorista[0]->motorista;
						if($personas==null){
							return 'No se le ha sido asignado ningun motorista.';
							
							
						}
							
					
						else{
							return $personas;
							
						}
					
				})
			->addColumn('vehiculo',function($dt){
					
						//$sql = "select concat('#Placa: ',numPlaca,' ','marca: ',marca) as vehiculo from cat_vehiculos where idVehiculo=
						//	(select idVehiculo from detalle_vehiculo where idSolicitud=".$dt->idSolicitud.")";
						//$vehiculos = DB::select(DB::raw($sql));
						/*$vehiculos= DB::table('cat_vehiculos')->join('idVehiculo',DB::table('detalle_vehiculo')->where('idSolicitud',$dt->idSolicitud)
						 	->select('idVehiculo'))
						 	->select(DB::raw('concat('.'"#Placa":'.',numPlaca,'.'" "'.',"marca:'.'" "'.',marca) as vehiculo'))->get();*/
						 $vehiculos = DB::table('cat_vehiculos as cv')->join('detalle_vehiculo as dv','cv.idVehiculo','=','dv.idVehiculo')
						 		 		->where('idSolicitud',$dt->idSolicitud)
						 				->select(DB::raw('concat('.'"#PLACA:"'.',numPlaca,'.'", "'.',"MARCA:"'.',marca,'.'", "'.',"TIPO VEHICULO:"'.',tipo) as vehiculo'))
						 				->get();
						 				//DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista')
						if($vehiculos==null){
							
							return 'No se le ha sido asignado ningun vehiculo.';
						}
							
					
						else{
							return $vehiculos[0]->vehiculo;
							
						}
					
				})
			 ->addColumn('motorista', function($dt){
			 		
					$sql = "select concat(nombre,' ',apellido) as motorista from cat_motorista where idMotorista=(select idMotorista from detalle_vehiculo 
							where idSolicitud=".$dt->idSolicitud.")";
					$motorista = DB::select(DB::raw($sql));
					if($motorista==null){
							
							return 'No se le ha sido asignado ningun motorista.';
						}
							
					
						else{
							return $motorista[0]->motorista;
							
						}
					
				})
			 ->addColumn('asignacion', function ($dt) {
            	if($dt->idEstado == 1 ||  $dt->nombreEstado ==="REPROGRAMADA"){
                	return '<a href="'.route('transporte.cambiarestado',['idEstado' =>4,'idSoliTrp' => Crypt::encrypt($dt->idSolicitud)]).'" class="btn btn-xs btn-warning btn-perspective"><i class="fa fa-pencil-square-o"></i> Suspender</a>';
				} 
				

            })
			->make(true);
			}
			
		}

		public function asignarVehiculo(Request $request){

				//dd($request->all());
			
				$id_solicitud = Crypt::decrypt($request->idSolicitud);
				//obteniendo la solicitud por medio del idSolicitud
	       		
				$trp_solicitud = Trp_Solicitud::find($id_solicitud);
				// ID= 2 DE ASIGNADO VEHICULO EN LA SOLICITUD

	    		$trp_solicitud->idEstado=2;
	    		$trp_solicitud->save();
	       		//dd($request->all());
			  
	    		

		       		$detallevehiculo = new DetalleVehiculo();

		       		$detallevehiculo->idSolicitud=$id_solicitud;
					if($trp_solicitud->conMotorista==1){
						if($request->idMotorista!= null){
							$detallevehiculo->idMotorista=$request->idMotorista;
						}
						else{
							Session::flash('msnError', 'Error: Seleccione un motorista!');
							return $this->edit($request->idSolicitud);
						}
					}
					
					if($request->idVehiculo!= null){
						$detallevehiculo->idVehiculo=$request->idVehiculo;
					}
					else{
						Session::flash('msnError', 'Error: Seleccione un vehiculo!');
						return $this->edit($request->idSolicitud);
						//return redirect()->route('transporte.asignarvehiculo')->with('idSoliTrp',$idSoliTrp);
					}
					
		       		$detallevehiculo->idUsuarioCrea=Auth::User()->idUsuario;
		       		$detallevehiculo->fechaCreacion=date('Y-m-d H:i:s');
		       		$detallevehiculo->save();
		       		//dd($detallevehiculo);
		       		Session::flash('msnExito', 'SE HA ASIGNADO VEHICULO A LA SOLICITUD CORRECTAMENTE!');
		       		return redirect()->route('solicitudes.est');
	    }
	       		
	    
  		
			
		
	

		public function edit($idSoliTrp){
			//desencriptando el id de solicitud que se va a editar
			//por seguridad se manda encryptado via get en el url 
			$id_solicitud = Crypt::decrypt($idSoliTrp);
			//obteniendo la solicitud por medio del idSolicitud
       		$trpsolicitud = Trp_Solicitud::findOrFail($id_solicitud);
       		//primero traemos todas las solicitudes que esten en estado asignado o en transito y 
       		//que tengan la misma fecha de la solicitud que queres asignar 
       		$solicitudes=Trp_Solicitud::where('fechaTransporte',$trpsolicitud->fechaTransporte)->whereIn('idEstado',[2,3,7])->get();



				
			$data = ['title' 			=> 'Mision Oficial y Transporte:'
				,'subtitle'			=> 'Asignar Vehiculo'
				,'breadcrumb' 		=> [
		 		['nom'	=>	'Administrador solicitudes', 'url' => route('solicitudes.est')],
		 		['nom'	=>	'Transporte', 'url' => '#']
			]];
			//obteniendo las personas que se ingresaron en la solicitud
       		$detallepersonas = DB::table('detalle_personas')->where('idSolicitud',$id_solicitud)->get();
			//$data['detallepersonas']=$detallepersonas;
			for($i=0;$i<count($detallepersonas);$i++){
				$empleado = CatEmpleados::where('idEmpleado',$detallepersonas[$i]->idEmpleado)->first();
				$data['personas'][$i]= $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado;
   			}
			
			$data['trpsolicitud'] = $trpsolicitud;
			//$data['catvehiculos'] = $catvehiculos;
			//$data['motoristas'] = $motoristas;
			
			//dd (json_encode($catvehiculos));
			//dd($data);
			//return count($data['personas']);
			//return CatVehiculo::where('idMotorista',1)->lists('numPlaca','idVehiculo');
			
			//return view('solicitudes.establecimientos.asignarvehiculo',$data);
			//dd(count($solicitudes));
			if(count($solicitudes)>1){
  				//recorremos las solicitudes
	  			foreach ($solicitudes as $solicitud) {
	  					//como ya sabes que son la misma fecha solo verificamos si son de la misma hora
	  					//de ahi verificamos las solicitudes con la misma hora  
	  						$soli[]=$solicitud->idSolicitud;
	  			}
	  			//dd($soli);
	  			$vehiculo=DetalleVehiculo::whereIn('idSolicitud',$soli)->select('idVehiculo')->distinct()->get();
	  			$motorista=DetalleVehiculo::whereIn('idSolicitud',$soli)->select('idMotorista')->distinct()->get();
	  			//return $vehiculo;
	  			//dd($vehiculo);
	  			foreach ($vehiculo as $veh) {
  							$idveh[]=$veh->idVehiculo;
  						}
  						
       					//return $idveh;
       					$catvehiculos= CatVehiculo::whereNotIn('idVehiculo',$idveh)->distinct()->get();

  						foreach ($motorista as $moto) {
  							$idmoto[]=$moto->idMotorista;
       					}
       					
       					//return $idmoto;
       					$motoristas = CatMotorista::whereNotIn('idMotorista',$idmoto)
       					  ->select('cat_motorista.idMotorista',DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista'))
       					  ->distinct()->get();
       					//return $catvehiculos;
       					$data['catvehiculos'] = $catvehiculos;
						$data['motoristas'] = $motoristas;
						return view('solicitudes.establecimientos.asignarvehiculo',$data);

  			}

  			else{
  					
  					$catvehiculos = DB::table('cat_vehiculos')
									->join('cat_motorista','cat_vehiculos.idMotorista','=','cat_motorista.idMotorista')
									->select('idVehiculo','numPlaca','marca','modelo','tipo','cat_motorista.idMotorista')
									->get();
					$motoristas = DB::table('cat_motorista')->select('cat_motorista.idMotorista',DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista'))
						          ->get();
					
					$data['catvehiculos'] = $catvehiculos;
					$data['motoristas'] = $motoristas;
					//dd($data);
					return view('solicitudes.establecimientos.asignarvehiculo',$data);
									          
  			}
  					
  				
 }

		public function asignarKms($idSoliTrp){
			//desencriptando el id de solicitud que se va a editar
			//por seguridad se manda encryptado via get en el url 
			$id_solicitud = Crypt::decrypt($idSoliTrp);
			//obteniendo la solicitud por medio del idSolicitud
       		$trpsolicitud = Trp_Solicitud::findOrFail($id_solicitud);
       		//primero traemos todas las solicitudes que esten en estado asignado o en transito y 
       		//que tengan la misma fecha de la solicitud que queres asignar 
       		$solicitudes=Trp_Solicitud::where('fechaTransporte',$trpsolicitud->fechaTransporte)->whereIn('idEstado',[2,3,7])->get();



				
			$data = ['title' 			=> 'Mision Oficial y Transporte:'
				,'subtitle'			=> 'Asignar Vehiculo'
				,'breadcrumb' 		=> [
		 		['nom'	=>	'Administrador solicitudes', 'url' => route('solicitudes.est')],
		 		['nom'	=>	'Transporte', 'url' => '#']
			]];
			//obteniendo las personas que se ingresaron en la solicitud
       		$detallepersonas = DB::table('detalle_personas')->where('idSolicitud',$id_solicitud)->get();
			//$data['detallepersonas']=$detallepersonas;
			for($i=0;$i<count($detallepersonas);$i++){
				$empleado = CatEmpleados::where('idEmpleado',$detallepersonas[$i]->idEmpleado)->first();
				$data['personas'][$i]= $empleado->nombresEmpleado.' '.$empleado->apellidosEmpleado;
   			}
			
			$data['trpsolicitud'] = $trpsolicitud;
			//$data['catvehiculos'] = $catvehiculos;
			//$data['motoristas'] = $motoristas;
			
			//dd (json_encode($catvehiculos));
			//dd($data);
			//return count($data['personas']);
			//return CatVehiculo::where('idMotorista',1)->lists('numPlaca','idVehiculo');
			
			//return view('solicitudes.establecimientos.asignarvehiculo',$data);
			//dd(count($solicitudes));
			if(count($solicitudes)>1){
  				//recorremos las solicitudes
	  			foreach ($solicitudes as $solicitud) {
	  					//como ya sabes que son la misma fecha solo verificamos si son de la misma hora
	  					//de ahi verificamos las solicitudes con la misma hora  
	  						$soli[]=$solicitud->idSolicitud;
	  			}
	  			//dd($soli);
	  			$vehiculo=DetalleVehiculo::whereIn('idSolicitud',$soli)->select('idVehiculo')->distinct()->get();
	  			$motorista=DetalleVehiculo::whereIn('idSolicitud',$soli)->select('idMotorista')->distinct()->get();
	  			//return $vehiculo;
	  			//dd($vehiculo);
	  			foreach ($vehiculo as $veh) {
  							$idveh[]=$veh->idVehiculo;
  						}
  						
       					//return $idveh;
       					$catvehiculos= CatVehiculo::whereNotIn('idVehiculo',$idveh)->distinct()->get();

  						foreach ($motorista as $moto) {
  							$idmoto[]=$moto->idMotorista;
       					}
       					
       					//return $idmoto;
       					$motoristas = CatMotorista::whereNotIn('idMotorista',$idmoto)
       					  ->select('cat_motorista.idMotorista',DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista'))
       					  ->distinct()->get();
       					//return $catvehiculos;
       					$data['catvehiculos'] = $catvehiculos;
						$data['motoristas'] = $motoristas;
						return view('solicitudes.establecimientos.asignarvehiculo',$data);

  			}

  			else{
  					
  					$catvehiculos = DB::table('cat_vehiculos')
									->join('cat_motorista','cat_vehiculos.idMotorista','=','cat_motorista.idMotorista')
									->select('idVehiculo','numPlaca','marca','modelo','tipo','cat_motorista.idMotorista')
									->get();
					$motoristas = DB::table('cat_motorista')->select('cat_motorista.idMotorista',DB::raw('concat(cat_motorista.nombre,'.'" "'.',cat_motorista.apellido) as motorista'))
						          ->get();
					
					$data['catvehiculos'] = $catvehiculos;
					$data['motoristas'] = $motoristas;
					//dd($data);
					return view('solicitudes.establecimientos.asignarkilometraje',$data);
									          
  			}
  					
  				
 }

		public function	getInformacionEvents(Request $request){

				
    			$solicitud = Trp_Solicitud::where('idSolicitud',$request->idSolicitud)->first();
    			return response()->json($solicitud);
		}
		public function getEvents(){
				$solicitudes=Trp_Solicitud::whereIn('idEstado',[2,3])->get();
				//return $solicitudes;
			for($i=0;$i<count($solicitudes);$i++) {

						$usuario= User::find($solicitudes[$i]->idUsuarioCrea);
						$detalleV= DetalleVehiculo::where('idSolicitud',$solicitudes[$i]->idSolicitud)->first();
						$vehiculo=CatVehiculo::where('idVehiculo',$detalleV->idVehiculo)->first();
						if($detalleV->idMotorista!=null){
							$motorista= CatMotorista::findOrFail($detalleV->idMotorista);
						}
						$starDate=$solicitudes[$i]->fechaTransporte.'T'.$solicitudes[$i]->horaInicio;
						if($solicitudes[$i]->horaFin==null){
					 		$endDate=null;
						}			
						else{
						$endDate=$solicitudes[$i]->fechaTransporte.'T'.$solicitudes[$i]->horaInicio;
						}
						
						 $data[$i]['title']= $motorista->nombre.' '.$motorista->apellido;
						 $data[$i]['description']=$solicitudes[$i]->lugar;
						 $data[$i]['solicitado']=$usuario->nombresUsuario.' '.$usuario->apellidosUsuario;
						 $data[$i]['vehiculo']='#PLACA: '.$vehiculo->numPlaca.' '.'MARCA: '.$vehiculo->marca.' '.'TIPO: '.$vehiculo->tipo;
						 $data[$i]['hInicio']=$solicitudes[$i]->horaInicio;
						 $data[$i]['hFin']=$solicitudes[$i]->horaFin;
						 $data[$i]['start']=$starDate;
               			 $data[$i]['url']="";
               			 $data[$i]['end']=$endDate;
               			 $data[$i]['id']=$solicitudes[$i]->idSolicitud;
               			 $data[$i]['color']=$motorista->color;
               			 $data[$i]['textColor']='#070707';
               			
               				 /*"title"=> $motorista->nombre.' '.$motorista->apellido, 
                			 "start"=> $starDate, 
                			  "url"=>"",
                			  "end"=>$endDate,
                			  );*/
					
				//$ii++;
			}
			return json_encode($data);
			//return response()->json($data);

		}

		public function showCronograma(){
			$data = ['title' 			=> 'Mision Oficial y Transporte:'
					,'subtitle'			=> 'Asignar Vehiculo'
					,'breadcrumb' 		=> [
			 		['nom'	=>	'Administrador solicitudes', 'url' => route('solicitudes.est')],
			 		['nom'	=>	'Transporte', 'url' => '#']
				]];

	
			return view('solicitudes.establecimientos.cronograma',$data);
		}

		public function update(TrpSoliRequest $request){
			 
			// return $this->edit($request->idSolicitud);
			/*if(in_array(DB::table('detalle_personas')->lists('idDetalleP'), $request->idDetalleP)
				return true;
			else
				return false;*/
				
										       			
			//dd($request->all());
			
			//DESENCRIPTANDO EL IDSOLICITUD
			 $id_solicitud = Crypt::decrypt($request->idSolicitud);
			//obteniendo la solicitud por medio del idSolicitud
			 //BUSCANDO LA SOLICITUD EN LA BASE DE DATOS
       		$trp_Solicitud = Trp_Solicitud::findOrFail($id_solicitud);
       		//primero comparamos si la fechaDesde es igual o mayor que que la fecha actual 
      		//la fechaSolicitudDesde es mayor que la fecha actual
    		if(date('Y-m-d',strtotime($request->fechaSolicitudD)) > date('Y-m-d'))
    			//si es mayor se obvia la validacion de que la horaDesde desde sea mayor que la hora actual
    			//ahora si la fecha solicitudHasta es igual a fecha de solicitudDesde
    			if(date('Y-m-d',strtotime($request->fechaSolicitudH)) == date('Y-m-d',strtotime($request->fechaSolicitudD)))
    					//si es cierto significa que las dos son mayores de la fecha actual
    				//ahora solo hay que comparar que la horaFin sea mayor que la horaInicio
    				if(date('H:i:s',strtotime($request->horaFin)) > date('H:i:s',strtotime($request->horaInicio)))
					{
      					 	//aqui se hace la magia se ingresa la solicitud
    						$trp_Solicitud->fechaSolicitudDesde=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudD.' '. $request->horaInicio));
								
							$trp_Solicitud->fechaSolicitudHasta=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudH.' '. $request->horaFin));
								
							$trp_Solicitud->lugar=$request->lugar;
					        
					        $trp_Solicitud->descripcion=$request->descripcion;
					       
					        //idEstado 1 como constante ya que el estado INGRESADO
					         $trp_Solicitud->idEstado=$request->catestado;
					      	//Input::get('members');

					        $trp_Solicitud->idUsuarioModifica=Auth::User()->idUsuario;
					        $trp_Solicitud->fechaModificacion=date('Y-m-d H:i:s');
					        $trp_Solicitud->save();
					        
					        if(count($request->idDetalleP)>0){
					        	//comparo cuantas personas hay en la solicitud con las que traigo del update 
					        	//sin son iguales se pudo a ver modificado nada mas el nombre de la persona
					        	if(Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona')==count($request->mytext))
					        		//si hago un for each y un where a la base por cada 
					        		//si son iguales solo hay que comparar que no se haya modificado los detalles de personas
							       {		
							       			foreach ($request->idDetalleP as $dep) {
							       				foreach ($request->mytext as $person) {

							       					$detalle_personas = Detalle_Personas::find($dep);
									       			$detalle_personas->persona=strtoupper($person);
											       	
											       	$detalle_personas->save();
											       Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       					return redirect()->route('solicitudes.est');

								       			}

							       			}
							       			
									 }
							      else if(count($request->mytext > Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona'))){
							      			
								      		for($i=0;$i<count($request->mytext);$i++){
								      			foreach ($request->idDetalleP as $dep) {
								      				if(Detalle_Personas::find($dep)!=null)
							       					{		$detalle_personas = Detalle_Personas::find($dep);
											       			$detalle_personas->persona=strtoupper($request->mytext[$i]);
													       	
													       	$detalle_personas->save();
										       		}
								      			}
								      				for($i=count($request->idDetalleP);$i<count($request->mytext);$i++){
								      					$detallep = new Detalle_Personas();
										       			$detallep->persona=strtoupper($request->mytext[$i]);
											       		$detallep->idSolicitud=$id_solicitud;
											       		$detallep->idUsuarioCrea=Auth::User()->idUsuario;
											       		$detallep->save(); 
											       	}

								      		}
								       		
								       	}
							       			
							       				
							       		
									       		
							}
							Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							return redirect()->route('solicitudes.est');
    				}
    				//si la horaFin no es mayor error no se puede ingresar.	
					else{
							Session::flash('msnError', 'Error la HORA FIN TIENE QUE SER  MAYOR A LA HORA INICIO!');
					        //return redirect()->route('nuevasolicitud');
					        return $this->edit($request->idSolicitud);

					}
				//si la fecha hasta es mayor no se hace la validacion de horaFin
				else {
					if(date('Y-m-d',strtotime($request->fechaSolicitudH)) > date('Y-m-d',strtotime($request->fechaSolicitudD)))
					{	//aqui se hace la magia se ingresa la solicitud	
						$trp_Solicitud->fechaSolicitudDesde=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudD.' '. $request->horaInicio));
								
							$trp_Solicitud->fechaSolicitudHasta=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudH.' '. $request->horaFin));
								
							$trp_Solicitud->lugar=$request->lugar;
					        
					        $trp_Solicitud->descripcion=$request->descripcion;
					       
					        //idEstado 1 como constante ya que el estado INGRESADO
					         $trp_Solicitud->idEstado=$request->catestado;
					      	//Input::get('members');

					        $trp_Solicitud->idUsuarioModifica=Auth::User()->idUsuario;
					        $trp_Solicitud->fechaModificacion=date('Y-m-d H:i:s');
					        $trp_Solicitud->save();
					        
					        if(count($request->idDetalleP)>0){
					        	//comparo cuantas personas hay en la solicitud con las que traigo del update 
					        	//sin son iguales se pudo a ver modificado nada mas el nombre de la persona
					        	if(Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona')==count($request->mytext))
					        		//si hago un for each y un where a la base por cada 
					        		//si son iguales solo hay que comparar que no se haya modificado los detalles de personas
							       {		
							       			foreach ($request->idDetalleP as $dep) {
							       				for($i=0;$i<count($request->mytext);$i++){
							       					$detalle_personas = Detalle_Personas::find($dep);
									       			$detalle_personas->persona=strtoupper($request->mytext[$i]);
											       	
											       	$detalle_personas->save();
											       	
											       	Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       					return redirect()->route('solicitudes.est');
								       			}

							       			}
							       			
									}
							       	else if(count($request->mytext > Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona'))){
							       			
								      		for($i=0;$i<count($request->mytext);$i++){
								      			foreach ($request->idDetalleP as $dep) {
								      				if(Detalle_Personas::find($dep)!=null)
							       					{		$detalle_personas = Detalle_Personas::find($dep);
											       			$detalle_personas->persona=strtoupper($request->mytext[$i]);
													       	
													       	$detalle_personas->save();
													       	

										       		}
								      			}
								      					for($i=count($request->idDetalleP);$i<count($request->mytext);$i++){
								      					$detallep = new Detalle_Personas();
										       			$detallep->persona=strtoupper($request->mytext[$i]);
											       		$detallep->idSolicitud=$id_solicitud;
											       		$detallep->idUsuarioCrea=Auth::User()->idUsuario;
											       		$detallep->save(); 
											       	}
 

								      		}
								       		
								       	}
									       		
							       	}
							       	Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       	return redirect()->route('solicitudes.est');
    				
					}
					else{
						Session::flash('msnError', 'Error la Fecha Hasta no puede ser menor que la Fecha Desde!');
					        //return redirect()->route('nuevasolicitud');	
							return $this->edit($request->idSolicitud);
					}

				}
					

    			
      		else{ 
      			//si la fechaSolicitudDesde es igual a la fecha actual
      			if(date('Y-m-d',strtotime($request->fechaSolicitudD)) == date('Y-m-d'))
    				//si es cierto hay que comparar que la hora inicioDesde sea mayor que la hora actual
	      			if(date('H:i:s',strtotime($request->horaInicio)) > date('H:i:s'))
	      				
	      				
	      				//si es mayor que horaIniico comparamos si la fechaSolicitudHasta es igual a la FechaDesde
	      				if(date('Y-m-d',strtotime($request->fechaSolicitudH)) == date('Y-m-d',strtotime($request->fechaSolicitudD)))
	      					//si es igual compramos la horaFin sea mayor que la HoraInicio
	      					if(date('H:i:s',strtotime($request->horaFin)) > date('H:i:s',strtotime($request->horaInicio)))
	      					{   //aqui se hace la magia se ingresa la solicitud
	      						$trp_Solicitud->fechaSolicitudDesde=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudD.' '. $request->horaInicio));
								
							$trp_Solicitud->fechaSolicitudHasta=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudH.' '. $request->horaFin));
								
							$trp_Solicitud->lugar=$request->lugar;
					        
					        $trp_Solicitud->descripcion=$request->descripcion;
					       
					        //idEstado 1 como constante ya que el estado INGRESADO
					         $trp_Solicitud->idEstado=$request->catestado;
					      	//Input::get('members');

					        $trp_Solicitud->idUsuarioModifica=Auth::User()->idUsuario;
					        $trp_Solicitud->fechaModificacion=date('Y-m-d H:i:s');
					        $trp_Solicitud->save();
					        
					        if(count($request->idDetalleP)>0){
					        	//comparo cuantas personas hay en la solicitud con las que traigo del update 
					        	//sin son iguales se pudo a ver modificado nada mas el nombre de la persona
					        	if(Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona')==count($request->mytext))
					        		//si hago un for each y un where a la base por cada 
					        		//si son iguales solo hay que comparar que no se haya modificado los detalles de personas
							       {		
							       			foreach ($request->idDetalleP as $dep) {
							       				foreach ($request->mytext as $person) {
							       					$detalle_personas = Detalle_Personas::find($dep);
									       			$detalle_personas->persona=strtoupper($person);
											       	
											       	$detalle_personas->save();
											       Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       					return redirect()->route('solicitudes.est');

								       			}

							       			}
							       			
									       	}
							       		else if(count($request->mytext > Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona'))){
							       			
								      		for($i=0;$i<count($request->mytext);$i++){
								      			foreach ($request->idDetalleP as $dep) {
								      				if(Detalle_Personas::find($dep)!=null)
							       					{		$detalle_personas = Detalle_Personas::find($dep);
											       			$detalle_personas->persona=strtoupper($request->mytext[$i]);
													       	
													       	$detalle_personas->save();
													      
										       		}
								      			}
								      					for($i=count($request->idDetalleP);$i<count($request->mytext);$i++){
								      					$detallep = new Detalle_Personas();
										       			$detallep->persona=strtoupper($request->mytext[$i]);
											       		$detallep->idSolicitud=$id_solicitud;
											       		$detallep->idUsuarioCrea=Auth::User()->idUsuario;
											       		$detallep->save(); 
											       	}


								      		}
								       		
								       	}
									       		
							       	}
							       	Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       return redirect()->route('solicitudes.est');
	      					}
	      					
	      					//sino es mayor la horaFin mandamos error y no se ingresa
	      					else
	      					{
	      						Session::flash('msnError', 'Error la HORA FIN TIENE QUE SER  MAYOR A LA HORA INICIO!');
						        //return redirect()->route('nuevasolicitud');
						        return $this->edit($request->idSolicitud);
	      					}
	      				//si la fechaHasta no es igual a la fechaDesde no se compara la horaFin con HoraInicio	
	      				else{
	      					//aqui se hace la magia se ingresa la solicitud
	      				  if(date('Y-m-d',strtotime($request->fechaSolicitudH)) > date('Y-m-d',strtotime($request->fechaSolicitudD))){
	      					$trp_Solicitud->fechaSolicitudDesde=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudD.' '. $request->horaInicio));
								
							$trp_Solicitud->fechaSolicitudHasta=date('Y-m-d H:i:s',strtotime($request->fechaSolicitudH.' '. $request->horaFin));
								
							$trp_Solicitud->lugar=$request->lugar;
					        
					        $trp_Solicitud->descripcion=$request->descripcion;
					       
					        //idEstado 1 como constante ya que el estado INGRESADO
					         $trp_Solicitud->idEstado=$request->catestado;
					      	//Input::get('members');

					        $trp_Solicitud->idUsuarioModifica=Auth::User()->idUsuario;
					        $trp_Solicitud->fechaModificacion=date('Y-m-d H:i:s');
					        $trp_Solicitud->save();
					        
					        if(count($request->idDetalleP)>0){
					        	//comparo cuantas personas hay en la solicitud con las que traigo del update 
					        	//sin son iguales se pudo a ver modificado nada mas el nombre de la persona
					        	if(Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona')==count($request->mytext))
					        		//si hago un for each y un where a la base por cada 
					        		//si son iguales solo hay que comparar que no se haya modificado los detalles de personas
							       {		
							       			foreach ($request->idDetalleP as $dep) {
							       				foreach ($request->mytext as $person) {
							       					$detalle_personas = Detalle_Personas::find($dep);
									       			$detalle_personas->Persona=strtoupper($person);
											       	
											       	$detalle_personas->save();
											       	Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       					return redirect()->route('solicitudes.est');
								       			}

							       			}
							       			
									       	}
							       		else if(count($request->mytext > Detalle_Personas::where('idSolicitud',$id_solicitud)->count('persona'))){
							       			
								      		for($i=0;$i<count($request->mytext);$i++){
								      			foreach ($request->idDetalleP as $dep) {
								      				if(Detalle_Personas::find($dep)!=null)
							       					{		$detalle_personas = Detalle_Personas::find($dep);
											       			$detalle_personas->Persona=strtoupper($request->mytext[$i]);
													       	
													       	$detalle_personas->save();
										       		}
								      			}
								      					for($i=count($request->idDetalleP);$i<count($request->mytext);$i++){
								      					$detallep = new Detalle_Personas();
										       			$detallep->persona=strtoupper($request->mytext[$i]);
											       		$detallep->idSolicitud=$id_solicitud;
											       		$detallep->idUsuarioCrea=Auth::User()->idUsuario;
											       		$detallep->save(); 
											       	}

								      		}
								       		
								       	}
									       		
							       	}
							       	Session::flash('msnExito', 'SE MODIFICADO LA SOLICITUD CORRECTAMENTE!');
							       return redirect()->route('solicitudes.est');
							}
							else{
								Session::flash('msnError', 'Error la Fecha Hasta no puede ser menor que la Fecha Desde!');
					        	return $this->edit($request->idSolicitud);
							}
	      				}
	      				
	      			//si la horaInicio no es mayor que la hora actual se manda un error	
	      			else{	
	      				
	      				Session::flash('msnError', 'Error la HORA INICIO TIENE QUE SER IGUAL O MAYOR A LA ACTUAL!');
				         //return view('solicitudes.establecimientos.nuevasolicitud',$data);
	      				//return redirect()->route('nuevasolicitud');
	      				return $this->edit($request->idSolicitud);
	      			}
      		 	
					  
				}
      			
					
       		
		}

		
//guardar una solicitud
		public function store(TrpSoliRequest $request)	
    	{	
    		//dd($request->all());
    		
    		//primero comparamos si la fechaTransporte es mayor que que la fecha actual 
      		//la fechaSolicitudDesde es mayor que la fecha actual
    		if(date('Y-m-d',strtotime($request->fechaSolicitudD)) > date('Y-m-d')){
    		 	//aqui se hace la magia se ingresa la solicitud
    						$var =$this->addSolicitud($request);
    						if($var){
    							//return $var;
    							//comprobamos que la hora fin sea distinto de null para hacer la resta.
    							if($request->horaFin!=null){
									$dif=date("H:i", strtotime("00:00") + strtotime($request->horaFin) - strtotime($request->horaInicio));
									$hora=explode(":", $dif);
									$difetimes=intval($hora[0]);					
    								//comprobamos que la diferencia sea mayor que 1 hora 
									if($difetimes >=1)	
									{    
										
	    								//Session::flash('msnExito', 'SE INGRESO LA SOLICITUD CORRECTAMENTE!');
	    							 	return response()->json(['status' => 200,'data'=>'1','message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);
	    							}
	    							else{
	    								return response()->json(['status' => 200,'data'=>'0','message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);	
	    							}
    							}
    							else{
    								return response()->json(['status' => 200,'data'=>'0','message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);
    							}
    							
    						}
    				
    			
			
    		}	
      		else{ 
      			//si la fechaTransporte no es mayor que la actual entonces verificamos si es igual
      			if(date('Y-m-d',strtotime($request->fechaSolicitudD)) == date('Y-m-d')){
    				//si es cierto comprobamos que la hora inicio sea mayor que la hora actual
	      			if(date('H:i:s',strtotime($request->horaInicio)) > date('H:i:s'))
	      			
	      					  //aqui se hace la magia se ingresa la solicitud
	      						$var =$this->addSolicitud($request);
	      						dd($var);
	    						if($var){
	    							//comprobamos que la hora fin sea distinto de null para hacer la resta.
									if($request->horaFin!=null){
										$dif=date("H:i", strtotime("00:00") + strtotime($request->horaFin) - strtotime($request->horaInicio));
										$hora=explode(":", $dif);
										$difetimes=intval($hora[0]);					
										//comprobamos que la diferencia sea mayor que 1 hora 
										if($difetimes >=1)	
										{    
											
		    								//Session::flash('msnExito', 'SE INGRESO LA SOLICITUD CORRECTAMENTE!');
		    							 	return response()->json(['status' => 200,'data'=>'1', 'message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);
		    							}
		    							else{
	    									return response()->json(['status' => 200,'data'=>'0','message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);	
	    								}
									}
									else{
										return response()->json(['status' => 200,'data'=>'0', 'message' => "SE INGRESO LA SOLICITUD CORRECTAMENTE"]);
									}

	    						}
	      			
				}
				else{
					return response()->json(['status' => 400,'data'=>'0', 'message' => "La fecha tiene que ser igual o mayor que hoy."]);
				}
					  
			}
	
	}

		//funcion para poner en estado cancelada si ya paso la fechadesolicitudHasta ya caduco 
		// y quedo en estado ingresada o reprogramada.		
		public function changeAllEstados(){

			$trpsolicitudes=DB::table('trp_solicitud')->get();

			foreach ($trpsolicitudes as $trpsolicitude) {
				if($trpsolicitude->fechaTransporte < date('Y-m-d') and in_array($trpsolicitude->idEstado,[1],true))
				{
					$transporte = Trp_Solicitud::find($trpsolicitude->idSolicitud);
					$transporte->idEstado=5;
					$transporte->save();

				}	

			}


		}				
					    
		

    	public function changeEstado($idEstado,$idSoliTrp){
			//dd($idEstado);
    		$id_solicitud = Crypt::decrypt($idSoliTrp);
    		$trp_solicitud = Trp_Solicitud::find($id_solicitud);
    		$trp_solicitud->idEstado=$idEstado;
    		$trp_solicitud->save();


    		return redirect()->route('solicitudes.est');

    	}
		
		public function concatPersonas($emp){
			
			$nombres='';
			for($i=0;$i<count($emp);$i++){
				$nomemp=DB::connection('sqlsrv')->select("select catep.nombresEmpleado + ' ' +catep.apellidosEmpleado as nombres
						from dnm_rrhh_si.RH.empleados  as catep
						where catep.idEmpleado=".$emp[$i]."");
			    $nombres.=$nomemp[0]->nombres;
				$nombres.=', ';
				//dd($nomemp);
			}
			return $nombres;
		}

		public  function addSolicitud($request){
			
								
								//dd($request->all());
								$trp_Solicitud= new Trp_Solicitud();
						       	
								$trp_Solicitud->fechaTransporte=date('Y-m-d',strtotime($request->fechaSolicitudD));
								$trp_Solicitud->horaInicio=date('H:i:s',strtotime($request->horaInicio));
								if($request->horaFin!=null){
									$trp_Solicitud->horaFin=date('H:i:s',strtotime($request->horaFin));
								}
								$trp_Solicitud->lugar=strtoupper($request->lugar);
						        
						        $trp_Solicitud->descripcion= strtoupper($request->descripcion);
								
								$trp_Solicitud->conMotorista=$request->conMotorista;
						        //idEstado 1 como constante ya que el estado INGRESADO
						         $trp_Solicitud->idEstado=1;
						      	//Input::get('members');

						        $trp_Solicitud->idUsuarioCrea=Auth::User()->idUsuario;
						        $trp_Solicitud->fechaCreacion=date('Y-m-d H:i:s');

						        $trp_Solicitud->save();
						        
						        $idNuevaSolicitud = $trp_Solicitud->idSolicitud;
						        if(count($request->idEmpleado)>0){
						        	//return dd($request->all());
							       	for($i=0;$i<count($request->idEmpleado);$i++){
							       		$detalle_personas = new Detalle_Personas();
							       		$detalle_personas->idEmpleado=$request->idEmpleado[$i];
							       		$detalle_personas->idSolicitud=$idNuevaSolicitud;
							       		$detalle_personas->idUsuarioCrea=Auth::User()->idUsuario;
							       		$detalle_personas->save();
							       	}
						       			
							    }
							    return 1;
		}
}

