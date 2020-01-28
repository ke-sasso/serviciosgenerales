<?php namespace App\Http\Controllers\Archivo;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Datatables;
use Validator;
use DB;
use Crypt;
use Mail;
use App\Models\rrhh\rh\Empleados;
use App\Models\cssp\CsspProductos;
use App\Models\dnm_ugda_si\PrestamoExpedientes;
use App\Models\dnm_ugda_si\bitacoraDePrestamo;
use App\Models\dnm_catalogos\SysUsuarios;
use App\Models\rrhh\rh\Jefes;
use App\Models\sim\sim_productos;
use App\Models\cssp\siic_cosmeticos;
use App\Models\dnm_establecimientos_si\est_establecimientos;
use App\Models\dnm_ugda_si\CAT\tiposExpedientes;

class PrestamosController extends Controller {

	public function index(){
	    $exp = tiposExpedientes::all();
		$data = ['title' 			=> 'Unidad de Archivo',
				'subtitle'			=> 'Préstamo de expedientes',
				'exp' => $exp
				];
		return view('archivo.index',$data);
	}

	public function prestamoEProductos($num){

    $tipoExp = 'Expedientes';
    $exp = Crypt::decrypt($num);
    $texp = tiposExpedientes::find($exp);

		$data = ['title' 			=> ''
				,'subtitle'			=> ''
				,'breadcrumb' 		=> [
                    ['nom'	=>	'Préstamo de Expedientes', 'url' => route('archivo.inicio')],
			 		['nom'	=>	$texp->nombreRegistroExpediente, 'url' => '#'],
				]];
		
			$data['num']=$num;	
		return view('archivo.prestamoEproductos.index',$data);
	}

	public function getDtEProductos(Request $request){
    $tipoReg = Crypt::decrypt($request->reg);

    switch ($tipoReg) {
      case '4':
          $productos = sim_productos::select('ID_PRODUCTO','NOMBRE_COMERCIAL')
            ->where(function ($query) use ($request) {
              
              if ($request->has('fregistro')) 
              {
                $query->whereIn('ID_PRODUCTO',$request->get('fregistro'));         
              }
             
            });
        break;
      case '3':
          $productos = CsspProductos::where('ACTIVO','!=','E')->select('ID_PRODUCTO','NOMBRE_COMERCIAL')
            ->where(function ($query) use ($request) {
              
              if ($request->has('fregistro')) 
              {
                $query->whereIn('ID_PRODUCTO',$request->get('fregistro'));         
              }
             
          });
        break;
        case '1':
          $productos = est_establecimientos::select('idEstablecimiento as ID_PRODUCTO','nombreComercial as NOMBRE_COMERCIAL')
            ->where(function ($query) use ($request) {
              
              if ($request->has('fregistro')) 
              {
                $query->whereIn('idEstablecimiento',$request->get('fregistro'));         
              }
             
          });
        break; 
        case '2':
          $productos = siic_cosmeticos::select(DB::Raw('noRegistro as ID_PRODUCTO, nombreComercial as NOMBRE_COMERCIAL'))
            ->where(function ($query) use ($request) {
              
              if ($request->has('fregistro')) 
              {
                $query->whereIn('noRegistro',$request->get('fregistro'));
              }
             
          });
        break;      
    }
		

		return Datatables::of($productos)
		->addColumn('ubicacion',function($dt){
                   $prestamo =  DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pre')
                   		->join('dnm_rrhh_si.RH.unidades as uni','pre.idUnidadSolicitante','=','uni.idUnidad')
                   ->where('pre.noRegistroExpediente',$dt->ID_PRODUCTO)
                   ->whereIn('pre.estadoPrestamo',[2,7,8])
                   ->orderBy('pre.idPrestamo','DESC')
                   ->select('uni.idUnidad','uni.nombreUnidad','pre.estadoPrestamo','pre.fechaCreacion')->first();                  
                   if($prestamo)   
                   {
                   	  return $prestamo->nombreUnidad;   	                 		                   
                   }
                   else
                   {
                   	return "EN ARCHIVO";
                   }             
                   
        })
		->addColumn('estado',function($dt){
                   $prestamo = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pre')
                   ->join('dnm_ugda_si.PRES.estadoPrestamo as est','pre.estadoPrestamo','=','est.idEstadoPrestamo')
                   ->where('pre.noRegistroExpediente',$dt->ID_PRODUCTO)
                   ->whereNotIn('pre.estadoPrestamo',[6])
                   ->orderBy('pre.idPrestamo', 'desc')
                   ->select('est.idEstadoPrestamo','est.nombreEstado') 
                   ->first();                  
                   if($prestamo)   
                   {  
                      if($prestamo->idEstadoPrestamo == 5)
                      {
                        return 'DISPONIBLE';
                      }
                      else
                      {
                        return $prestamo->nombreEstado;  
                      }
	                    	                   	                  	
                   }
                   else
                   {
                   		return "DISPONIBLE"; 
                   }             
                   
        })
        ->addColumn('prestar',function($dt){
                
            return '<a onclick="fcnprestar(\''.$dt->ID_PRODUCTO.'\');" class="btn btn-xs btn-primary btn-perspective"><i title="prestamo" class="fa fa-folder"></i></a>';
                    
        })
		->make(true);
	}

  public function findRegistrosToSelectize(Request $request)
  {
    $busqueda = ($request->q);
    $tipoReg = Crypt::decrypt($request->reg);
    $data = [];
    if(!$busqueda && $busqueda == '') return response()->json(array(), 400);
    
    switch ($tipoReg) {
      case '4':
          $data = sim_productos::select('ID_PRODUCTO','NOMBRE_COMERCIAL')
            ->where(function ($query) use ($busqueda) {
              
              return $query->where('ID_PRODUCTO','LIKE','%'.$busqueda.'%')
              ->orWhere('NOMBRE_COMERCIAL','LIKE','%'.$busqueda.'%');
             
            })
            ->select('ID_PRODUCTO','NOMBRE_COMERCIAL')
            ->take(10)->get();
        break;
      case '3':
          $data = CsspProductos::where('ACTIVO','!=','E')
          ->where(function ($query) use ($busqueda) {
            return $query->where('ID_PRODUCTO','LIKE','%'.$busqueda.'%')
              ->orWhere('NOMBRE_COMERCIAL','LIKE','%'.$busqueda.'%');
          })     
          ->select('ID_PRODUCTO','NOMBRE_COMERCIAL')
          ->take(10)->get();
        break;
      case '1':
          $data = est_establecimientos::where(function ($query) use ($busqueda) {
            return $query->where('idEstablecimiento','LIKE','%'.$busqueda.'%')
              ->orWhere('nombreComercial','LIKE','%'.$busqueda.'%');
          })     
          ->select('idEstablecimiento as ID_PRODUCTO','nombreComercial as NOMBRE_COMERCIAL')
          ->take(10)->get();
        break;
        case '2':
          $data = siic_cosmeticos::where(function ($query) use ($busqueda) {
            return $query->where('noRegistro','LIKE','%'.$busqueda.'%')
              ->orWhere('nombreComercial','LIKE','%'.$busqueda.'%');
          })     
          ->select(DB::Raw('noRegistro as ID_PRODUCTO, nombreComercial as NOMBRE_COMERCIAL'))
          ->take(10)->get();
        break;                  
    }

    return response()->json(array(
      'data'=>$data
    ));
  }

	public function EProductosPrestar(Request $request)
	{ 		
    $tipoReg = Crypt::decrypt($request->reg);
    $idproducto =  $request->param;
    $producto = null;
    switch ($tipoReg) {
      case '4':
          $producto = sim_productos::where('ID_PRODUCTO',$idproducto)
          ->select('ID_PRODUCTO','NOMBRE_COMERCIAL')
          ->first();
        break;
      case '3':
          $producto = CsspProductos::where('ID_PRODUCTO',$idproducto)
          ->select('ID_PRODUCTO','NOMBRE_COMERCIAL')
          ->first();
        break;
      case '1':
          $producto = est_establecimientos::where('idEstablecimiento',$idproducto)
          ->select('idEstablecimiento as ID_PRODUCTO','nombreComercial as NOMBRE_COMERCIAL')
          ->first();
        break;
      case '2':
          $producto = siic_cosmeticos::where('noRegistro',$idproducto)
          ->select(DB::Raw('noRegistro as ID_PRODUCTO, nombreComercial as NOMBRE_COMERCIAL'))
          ->first();
        break;
    }		

		$prestamo = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pre')
                   ->join('dnm_ugda_si.PRES.estadoPrestamo as est','pre.estadoPrestamo','=','est.idEstadoPrestamo')
                   ->where('pre.noRegistroExpediente',$idproducto)
                   ->whereNotIn('pre.estadoPrestamo',[6])
                   ->select('est.idEstadoPrestamo','est.nombreEstado','pre.estadoPrestamo','pre.idEmpleadoSolicitante')
                   ->orderBy('pre.idPrestamo', 'desc')->first();
        if($prestamo)
        {
        	if($prestamo->idEstadoPrestamo==5)
           	{
           		$producto->idestado = 5;
       			  $producto->nomestado = 'DISPONIBLE';
           	}
           	else
           	{              
           		$producto->idestado = $prestamo->estadoPrestamo;              
       			  $producto->nomestado = $prestamo->nombreEstado; 
           	}

          $emp = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
              ->join('dnm_rrhh_si.RH.plazasFuncionales as pla','emp.idPlazaFuncional','=','pla.idPlazaFuncional')
              ->join('dnm_rrhh_si.RH.unidades as uni','pla.idUnidad','=','uni.idUnidad')
              ->select('emp.nombresEmpleado','emp.apellidosEmpleado','pla.idUnidad','uni.nombreUnidad')
              ->where('emp.idEmpleado',$prestamo->idEmpleadoSolicitante)
              ->first();
          $producto->solicita=$emp;       		    		         
        }
        else
        {
        	$producto->idestado = 5;
       		$producto->nomestado = 'DISPONIBLE';
          $producto->solicita=null;
        }        
       
        $hoy = date("Y-m-d");        
        $mañana = date('d-m-Y', strtotime($hoy. ' + 1 days'));        
        $producto->mañana= $mañana;        
		
        return response()->json(['status' => 200, 'data' => $producto],200);		
	}

	public function SavePrestamo(Request $request){

		$v = Validator::make($request->all(),[          
            'idproducto'=>'required',
            'fecha'=>'required',
            'tipoprestamo'=>'required',
            'idunidad'=>'required'
                           
                ]);

    $v->setAttributeNames([         
        'idproducto'=>'PRODUCTO',
        'fecha'=>'FECHA',
        'tipoprestamo'=>'TIPO DE PRESTAMO',
        'idunidad'=>'UNIDAD'                   
    ]);

    if ($v->fails())
    { 
        $msg = "<ul class='text-warning'>";
        foreach ($v->messages()->all() as $err) {
            $msg .= "<li>$err</li>";
        }
        $msg .= "</ul>";
        return $msg;
    }
		DB::connection('sqlsrv')->beginTransaction();
		  try 
      {   
				$exp = Crypt::decrypt($request->idunidad);
				$texp= tiposExpedientes::where('idRegistroExpediente',$exp)->select('idUnidad')->first();
				$idunidad =$texp->idUnidad;

				$usuariomysql= Auth::User();

				$unidad= DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
					->join('dnm_rrhh_si.RH.plazasFuncionales as pla','emp.idPlazaFuncional','=','pla.idPlazaFuncional')
					->join('dnm_rrhh_si.RH.unidades as uni','pla.idUnidad','=','uni.idUnidad')
					->select('pla.idUnidad','uni.nombreUnidad','pla.idPlazaFuncional')
					->where('emp.idEmpleado',$usuariomysql->idEmpleado)
					->first();

        $excepcion = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.excepcionPrestamo')
                    ->where('idPlazaFuncional',$unidad->idPlazaFuncional)->count();

            /*se recupera datos para enviar correo*/
        $jefe= DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.jefes as j')
                      ->join('dnm_rrhh_si.RH.plazasFuncionales as pf','j.idPlazaFuncional','=','pf.idPlazaFuncional')
                      ->join('dnm_rrhh_si.RH.empleados as e','pf.idPlazaFuncional','=','e.idPlazaFuncional')
                      ->where('pf.idUnidad',$idunidad)
                      ->select('j.idJefe','pf.idPlazaFuncional','pf.nombrePlaza','pf.idUnidad','e.idEmpleado','e.nombresEmpleado','e.apellidosEmpleado', 'e.email')
                      ->first();
        $jefecssp = SysUsuarios::where('idEmpleado',$jefe->idEmpleado)
          ->select('idUsuario','idEmpleado','correo')
          ->first();

        $tipo = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.cat_tipo_prestamo')
                        ->where('idTipoPrestamo',$request->tipoprestamo)
                        ->select('nombreTipoPrestamo')
                        ->first();

        $data['empleado']= $usuariomysql;
        $data['unidad'] = $unidad;        
        $data['idempleado']=$jefecssp->idEmpleado;
        $data['tipoprestamo'] = $tipo->nombreTipoPrestamo;  

        if($excepcion>0)
        {
          //se crea el nuevo registro de prestamo
            $prestamo = new PrestamoExpedientes(); 
            $prestamo->fechaPrestamo = date('Y-m-d',strtotime($request->fecha));
            $prestamo->idEmpleadoSolicitante=$usuariomysql->idEmpleado;
            $prestamo->idUnidadSolicitante = $unidad->idUnidad;
            $prestamo->noRegistroExpediente = $request->idproducto;
            $prestamo->nombreExpediente = $request->nombreproducto;
            $prestamo->estadoPrestamo = 4;//autorizado para préstamo
            $prestamo->tipoPrestamo = $request->tipoprestamo;                
            $prestamo->idUnidadAutoriza = $unidad->idUnidad;
            $prestamo->idEmpleadoAutoriza = $usuariomysql->idEmpleado; 
            $prestamo->fechaAutorizacion = date('Y-m-d H:i:s');                     
            $prestamo->idUsuarioCrea =$usuariomysql->idUsuario;
            $prestamo->tipoRegistroUnidad = $idunidad;                  
            $prestamo->save();


            /*se inserta en la bitacora en evento*/
            bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'SOLICITUD INGRESADA');

            $data['prestamo'] = $prestamo;

            Mail::send('emails.prestamoExpediente.informarPrestamo',$data,function($msj) use ($data,$jefecssp)
            {
              $msj->from('solicitudes.administrativas@medicamentos.gob.sv','UGDA - Expedientes');
              $msj->subject('Solicitud de Préstamo de Expediente');
              $msj->to($jefecssp->correo);
              //$msj->to('luis.hernandez@medicamentos.gob.sv');
              $msj->bcc('rogelio.menjivar@medicamentos.gob.sv');
            }); 
        }
        else
        {

  				if($unidad->idUnidad==$idunidad)
  				{
  					$estado = 1;
  				}
  				else
  				{
  					$estado = 3;
  				}
                  //se crea el nuevo registro de prestamo                  
                  $prestamo = new PrestamoExpedientes(); 
                  $prestamo->fechaPrestamo = date('Y-m-d',strtotime($request->fecha));
                  $prestamo->idEmpleadoSolicitante=$usuariomysql->idEmpleado;
                  $prestamo->idUnidadSolicitante = $unidad->idUnidad;
                  $prestamo->noRegistroExpediente = $request->idproducto;
                  $prestamo->nombreExpediente = $request->nombreproducto;
                  $prestamo->estadoPrestamo = $estado;
                  $prestamo->tipoPrestamo = $request->tipoprestamo;                
                  $prestamo->idUnidadAutoriza = $idunidad;                         
                  $prestamo->idUsuarioCrea =$usuariomysql->idUsuario; 
                  $prestamo->tipoRegistroUnidad = $idunidad;                        
                  $prestamo->save();

                  $idmax = PrestamoExpedientes::where('idEmpleadoSolicitante',$usuariomysql->idEmpleado)->where('idUnidadSolicitante',$unidad->idUnidad)->where('noRegistroExpediente',$request->idproducto)->where('idUnidadAutoriza',$idunidad)->where('tipoRegistroUnidad',$idunidad)->max('idPrestamo');

                  /*se inserta en la bitacora en evento de insercion de solicitud*/
                  $bitacora = new bitacoraDePrestamo();
                  $bitacora->idPrestamo = $prestamo->idPrestamo; 
                  $bitacora->estadoPrestamo = $prestamo->estadoPrestamo;
                  $bitacora->observacion = 'SOLICITUD INGRESADA';
                  $bitacora->idUsuarioCrea = Auth::user()->idUsuario.'@'.$request->ip();                
                  $bitacora->save();
                  
                  $prestamo->idPrestamo = $idmax;  

                	if($estado==3){
                		
                    /*se inserta en la bitacora empleado al que se manda a autorizar*/                 
                    $bitacora->observacion = 'SOLICITUD ENVIADA PARA APROBACI&Oacute;N A : '.$jefe->nombresEmpleado.' '.$jefe->apellidosEmpleado;
                    $bitacora->save();

                			/*se envia msj al jefe de la unidad para que apruebe la solicitud de prestamo*/                                  

                    $data['prestamo'] = $prestamo;
                    $data['URL_APROBAR'] = env('URL_APROBAR');
                	  Mail::send('emails.prestamoExpediente.confirmar',$data,function($msj) use ($data,$jefecssp)
  					        {
                      $msj->from('solicitudes.administrativas@medicamentos.gob.sv','UGDA - Expedientes');
                      $msj->subject('Solicitud de Préstamo de Expediente');
    			            $msj->to($jefecssp->correo);
    			            $msj->bcc('rogelio.menjivar@medicamentos.gob.sv');
  					        });
                	} 
                  /*else
                  {
                    $data['prestamo'] = $prestamo;
                    Mail::send('emails.prestamoExpediente.informarPrestamo',$data,function($msj) use ($data,$jefecssp)
                    {
                      $msj->from('solicitudes.administrativas@medicamentos.gob.sv','UGDA - Expedientes');
                      $msj->subject('Solicitud de Préstamo de Expediente');
                      $msj->to($jefecssp->correo);
                      $msj->to('luis.hernandez@medicamentos.gob.sv');            
                      $msj->bcc('rogelio.menjivar@medicamentos.gob.sv');
                    });
                  } */          
        }

        DB::connection('sqlsrv')->commit();           
      } 
      catch(PDOException $e)
      {
                  DB::connection('sqlsrv')->rollback();
                  throw $e;
                  return $e->getMessage();
      }
  		
             
                   
      return response()->json(['state' => 'success']);
	}

	public function AutorizarPrestamo($idsolicitud,$idempleado,$idestado, Request $request){
		
		DB::connection('sqlsrv')->beginTransaction();

			$prestamo = PrestamoExpedientes::where('idPrestamo',$idsolicitud)->first();
      
			$usuariocssp = SysUsuarios::where('idEmpleado',$idempleado)
				->select('idUsuario','idEmpleado')->first();
			
			if($prestamo->estadoPrestamo==3)
			{
				$prestamo->estadoPrestamo = $idestado;
				$prestamo->idEmpleadoAutoriza = $idempleado;
				$prestamo->fechaAutorizacion = date('Y-m-d H:i:s');
				$prestamo->idUsuarioModifica = $usuariocssp->idUsuario;
				$prestamo->save();

        if($prestamo->estadoPrestamo==4)
        {
          $obs = 'SOLICITUD APROBADA';
        }
        elseif($prestamo->estadoPrestamo==6)
        {
          $obs = 'SOLICITUD DENEGADA';
        }

        /*se inserta en la bitacora en evento de autorizar o denegar*/
        bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,$usuariocssp->idUsuario.'@'.$request->ip(),$obs);

        $empleado= Empleados::findOrFail($prestamo->idEmpleadoSolicitante);

        $unidad= $empleado->plazaFuncional->unidad;

        $data['prestamo'] = $prestamo;
        $data['empleado'] = $empleado;
        $data['unidad'] = $unidad;
        //se envia un msj de notificacion de aprobacion de solicitud para prestamo
        /*Mail::send('emails.prestamoExpediente.confirmarAprobacion',$data,function($msj) use ($data)
            {
              //$msj->from('solicitudes.administrativas@medicamentos.gob.sv','UGDA - Expedientes');
              $msj->subject('Solicitud de Préstamo de Expediente');
              $msj->to('luis.hernandez@medicamentos.gob.sv');
              $msj->bcc('rogelio.menjivar@medicamentos.gob.sv');
            });*/
			}
			
		DB::connection('sqlsrv')->commit();
		$data['estado']=$prestamo->estadoPrestamo; 
		return view ('emails.prestamoExpediente.confirmacionprestamo',$data);
	}
	/*consultar los prestamos de cada usuario*/
	public function EProductosMisPrestamos(){
		$data = ['title' 			=> ''
				,'subtitle'			=> ''
				,'breadcrumb' 		=> [
			 		['nom'	=>	'Mis préstamos', 'url' => route('exp.prod.misprestamos')],
			 		['nom'	=>	'', 'url' => '#']
				
				]];	
		$estados = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.estadoPrestamo')
				//->where('idEstadoPrestamo','!=','5')
				->select('idEstadoPrestamo','nombreEstado')
				->orderBy('nombreEstado','ASC')
				->get();
		$data['estados']=$estados;	
				
		return view('archivo.prestamoEproductos.misprestamos',$data);
	}

	public function GetMisPrestamosEP(Request $request){
		$miusuario = Auth::user();
			
		$misprestamos = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pre')
			->join('dnm_ugda_si.PRES.estadoPrestamo as est','pre.estadoPrestamo','=','est.idEstadoPrestamo')
      ->join('dnm_rrhh_si.RH.unidades as u','pre.idUnidadAutoriza','=','u.idUnidad')
      ->leftjoin('dnm_rrhh_si.RH.empleados as ea','pre.idEmpleadoAutoriza','=','ea.idEmpleado')
			->where('pre.idEmpleadoSolicitante',$miusuario->idEmpleado)
      ->whereIn('pre.estadoPrestamo',[5,6,7,9,10])
			->select('pre.idPrestamo','pre.noRegistroExpediente','pre.nombreExpediente','pre.estadoPrestamo','est.nombreEstado','pre.fechaPrestamo','u.nombreUnidad as uautoriza',DB::raw('concat(ea.nombresEmpleado,\' \',ea.apellidosEmpleado) as empleadoAutoriza'),'pre.solicitudTransferida')
      ->where(function ($query) use ($request) {
        if ($request->has('idproducto')) 
        {
          $query->where('pre.noRegistroExpediente','LIKE','%'.$request->get('idproducto').'%');
        }
        if ($request->has('nomproducto')) 
        {
          $query->where('pre.nombreExpediente','LIKE','%'.$request->get('nomproducto').'%');
        }
        if ($request->has('fecha')) 
        {
          $fecha = date('Y-m-d',strtotime($request->get('fecha')));
          $query->where('pre.fechaPrestamo','LIKE','%'.$fecha.'%');
        }
        if ($request->has('estado')) 
        {
          $query->where('pre.estadoPrestamo','=',$request->get('estado'));
        }

      });
			

		return Datatables::of($misprestamos)
    ->editColumn('fechaPrestamo', function ($prestamos) {
                  return $prestamos->fechaPrestamo ? with(date('d-m-Y',strtotime($prestamos->fechaPrestamo))) : '';
    })
    ->editColumn('nombreEstado', function ($prestamos) {
        if($prestamos->estadoPrestamo==6)
        {
          return '<p class="text-danger">'.$prestamos->nombreEstado.'</p>';
        }
        else
        {
          return $prestamos->nombreEstado;
        }
    })		  
    ->addColumn('transfirio',function($dt){
      $transfirio = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as p')
            ->join('dnm_rrhh_si.RH.empleados as e','p.idEmpleadoSolicitante','=','e.idEmpleado')
            ->where('p.idPrestamo',$dt->solicitudTransferida)
            ->select(DB::raw('concat(e.nombresEmpleado,\' \',e.apellidosEmpleado) as etransfirio'))
            ->first();
      if($transfirio)
      {
        return $transfirio->etransfirio;
      }
                        
    })		
		
		->make(true);
	}

	public function RetornarArchivo(Request $request){
		$v = Validator::make($request->all(),[          
            'mridprestamo'=>'required',
            'mrfecha'=>'required'            
                           
                ]);

        $v->setAttributeNames([         
            'mridprestamo'=>'EXPEDIENTE',
            'mrfecha'=>'FECHA DE DEVOLUCIÓN'                             
        ]);

        if ($v->fails())
        { 
            $msg = "<ul class='text-warning'>";
            foreach ($v->messages()->all() as $err) {
                $msg .= "<li>$err</li>";
            }
            $msg .= "</ul>";
            return $msg;
        }
        DB::connection('sqlsrv')->beginTransaction();         
            try {             
                //se recupera el prestamo
                $prestamo =PrestamoExpedientes::where('idPrestamo',$request->mridprestamo)
                	->first(); 
                $prestamo->fechaDevolucion = date('Y-m-d',strtotime($request->mrfecha));              
                $prestamo->idUsuarioModifica = Auth::user()->idUsuario;
                $prestamo->estadoPrestamo = 7;                                 
                $prestamo->save(); 

                bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'EXPEDIENTE ENVIADO A ARCHIVO');               

            } catch(PDOException $e){
                DB::connection('sqlsrv')->rollback();
                throw $e;
                return $e->getMessage();
            }
        DB::connection('sqlsrv')->commit();        
                   
        return response()->json(['state' => 'success']);
	}

	public function GetEmpleadosUnidad(){
		$usuariocssp = SysUsuarios::where('idUsuario',Auth::user()->idUsuario)
				->select('idUsuario','idEmpleado')->first();

		$unidad= DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as emp')
					->join('dnm_rrhh_si.RH.plazasFuncionales as pla','emp.idPlazaFuncional','=','pla.idPlazaFuncional')					
					->select('pla.idUnidad')
					->where('emp.idEmpleado',$usuariocssp->idEmpleado)
					->first();

		$empleados = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados as e')
			->join('dnm_rrhh_si.RH.plazasFuncionales as pf','e.idPlazaFuncional','=','pf.idPlazaFuncional')
			->join('dnm_rrhh_si.RH.unidades as u','pf.idUnidad','=','u.idUnidad')
			->where('u.idUnidad',$unidad->idUnidad)
			->where('e.idEmpleado','!=',$usuariocssp->idEmpleado)
			->select('e.idEmpleado','e.nombresEmpleado','e.apellidosEmpleado');

		return Datatables::of($empleados)->make(true);
	}

	public function TransferirExpediente(Request $request){
		$v = Validator::make($request->all(),[          
            'mtidempleado'=>'required',
            'mtidprestamo'=>'required'                          
    ]);

    $v->setAttributeNames([         
        'mtidempleado'=>'EMPLEADO',
        'mtidprestamo'=>'EXPEDIENTE'                             
    ]);

    if ($v->fails())
    { 
        $msg = "<ul class='text-warning'>";
        foreach ($v->messages()->all() as $err) {
            $msg .= "<li>$err</li>";
        }
        $msg .= "</ul>";
        return $msg;
    }
    DB::connection('sqlsrv')->beginTransaction();         
        try {            
          
            //se recupera el prestamo
            $prestamo =PrestamoExpedientes::where('idPrestamo',$request->mtidprestamo)
            	->first();
            $prestamo->estadoPrestamo = 9;/*se marca como transferido*/
            $prestamo->save();           

            $newprestamo = new PrestamoExpedientes();
            $newprestamo->fechaPrestamo = date('Y-m-d H:i:s');
            $newprestamo->idEmpleadoSolicitante = $request->mtidempleado;
            $newprestamo->idUnidadSolicitante = $prestamo->idUnidadSolicitante;
            $newprestamo->noRegistroExpediente = $prestamo->noRegistroExpediente;
            $newprestamo->nombreExpediente = $prestamo->nombreExpediente;
            $newprestamo->estadoPrestamo = 8;/*se entrego al empleado*/
            $newprestamo->tipoPrestamo = $prestamo->tipoPrestamo;
            $newprestamo->idUnidadAutoriza = $prestamo->idUnidadAutoriza;
            $newprestamo->fechaAutorizacion = date('Y-m-d H:i:s');
            $newprestamo->idEmpleadoAutoriza = $prestamo->idEmpleadoAutoriza;                
            $newprestamo->idUsuarioCrea = Auth::user()->idUsuario;
            $newprestamo->solicitudTransferida = $prestamo->idPrestamo;                                
            $newprestamo->save();

            bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'EXPEDIENTE TRANSFERIDO A : '.$newprestamo->eSolicita->getNombreCompleto());          

            bitacoraDePrestamo::insertarBitacora($newprestamo->idPrestamo,$newprestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'EXPEDIENTE TRANSFERIDO POR : '.$prestamo->eSolicita->getNombreCompleto());                

        } catch(PDOException $e){
            DB::connection('sqlsrv')->rollback();
            throw $e;
            return $e->getMessage();
        }
    DB::connection('sqlsrv')->commit();        
               
    return response()->json(['state' => 'success']);
	}

  public function ConfirmarRecibido(Request $request)
  {    
    $idSolicitudes =$request->idPrestamos; 
   
    $prestamos = PrestamoExpedientes::whereIn('idPrestamo',$idSolicitudes)->get();

    DB::connection('sqlsrv')->beginTransaction();
    try 
    {

      foreach($prestamos as $pre)
      {        
        $pre->estadoPrestamo = 2;
        $pre->idUsuarioModifica = Auth::user()->idUsuario;
        $pre->save();

        if($pre->solicitudTransferida) 
        {
          $nomEmp = Empleados::where('idEmpleado',$pre->idEmpleadoSolicitante)
              ->select(DB::raw("concat(nombresEmpleado,' ',apellidosEmpleado) as nomEmpleado"))->first();
          /*se inserta en la bitacora en de la solicitud que transfirio*/
          bitacoraDePrestamo::insertarBitacora($pre->solicitudTransferida,9,Auth::user()->idUsuario.'@'.$request->ip(),'EXPEDIENTE ACEPTADO POR: '.$nomEmp->nomEmpleado);       

        }

        /*se inserta en la bitacora en evento recibir expediente*/
        bitacoraDePrestamo::insertarBitacora($pre->idPrestamo,$pre->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'EXPEDIENTE RECIBIDO'); 
      }

    } 
    catch(PDOException $e)
    {
        DB::connection('sqlsrv')->rollback();
        return response()->json(['status' => 400, 'message' => "<ul class='text-warning'><li>OCURRIÓ UN ERROR</li></ul>"]);   
    }
    DB::connection('sqlsrv')->commit(); 
    return response()->json(['status' => 200, 'message' => "Exito"]);

  }

  /**/
  public function solicitudesToAutorizar()
  {
    $data = ['title'      => ''
        ,'subtitle'     => ''
        ,'breadcrumb'     => [
          ['nom'  =>  'Solicitudes de préstamos', 'url' => route('solicitudes.to.autorizar')],
          ['nom'  =>  '', 'url' => '#']
        
        ]];

    $unidades = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.unidades')
          ->select('idUnidad','nombreUnidad')->get();
    $data['unidades'] = $unidades;    
        
    return view('archivo.solicitudesToAutorizar',$data);

  }

  public function getSolicitudesToAutorizar(Request $request)
  {
    $idunidad = Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->idUnidad;

    if($idunidad)
    {
      $solicitudes = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pe')
          ->join('dnm_rrhh_si.RH.empleados as e','pe.idEmpleadoSolicitante','=','e.idEmpleado')
          ->join('dnm_rrhh_si.RH.unidades as u','pe.idUnidadSolicitante','=','u.idUnidad')        
          ->select('pe.idPrestamo','pe.noRegistroExpediente','pe.nombreExpediente',DB::connection('sqlsrv')->raw('concat(e.nombresEmpleado,\' \',e.apellidosEmpleado) as eSolicita'),'u.nombreUnidad','pe.fechaPrestamo')
          ->where('estadoPrestamo',3)
          ->where('pe.idUnidadAutoriza',$idunidad)
          ->where(function ($query) use ($request) {
            if ($request->has('fidproducto')) 
            {
              $query->where('pe.noRegistroExpediente','LIKE','%'.$request->get('fidproducto').'%');
            }
            if ($request->has('fnomproducto')) 
            {
              $query->where('pe.nombreExpediente','LIKE','%'.$request->get('fnomproducto').'%');
            }
            if ($request->has('fusolicita')) 
            {
              $query->where('pe.idUnidadSolicitante',$request->get('fusolicita'));
            }
            if ($request->has('ffecha')) 
            {
              $fecha = date('Y-m-d',strtotime($request->get('ffecha')));
              $query->where('pe.fechaPrestamo','LIKE','%'.$fecha.'%');
            }          

        });

      return Datatables::of($solicitudes)
        ->editColumn('fechaPrestamo', function ($solicitudes) {
                    return $solicitudes->fechaPrestamo ? with(date('d-m-Y',strtotime($solicitudes->fechaPrestamo))) : '';
        }) 
        ->addColumn('autorizar', function ($dt) {
                    return '<a onclick="autorizar(\''.$dt->idPrestamo.'\',4);" class="btn btn-xs btn-perspective btn-success">Autorizar</a>';
        })
        ->addColumn('denegar', function ($dt) {
                    return '<a onclick="autorizar(\''.$dt->idPrestamo.'\',6);" class="btn btn-xs  btn-perspective btn-danger">Denegar</a>';
        })          
        ->make(true);
    }
  }

  public function AutorizarPrestamoDesdeSG(Request $request)
  { 
    DB::connection('sqlsrv')->beginTransaction();
    try 
    {      
      $prestamo = PrestamoExpedientes::where('idPrestamo',$request->idprestamo)->first();
      $prestamo->estadoPrestamo = $request->opcion;
      $prestamo->idEmpleadoAutoriza = Auth::user()->idEmpleado;
      $prestamo->fechaAutorizacion = date('Y-m-d H:i:s');
      $prestamo->idUsuarioModifica = Auth::user()->idUsuario;
      $prestamo->save(); 
     
      if($prestamo->estadoPrestamo==4)
      {
        $obs = 'SOLICITUD APROBADA';
      }
      elseif($prestamo->estadoPrestamo==6)
      {
        $obs = 'SOLICITUD DENEGADA';
      }

      /*se inserta en la bitacora en evento de autorizar o denegar*/
      bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),$obs); 

      $empleado= Empleados::findOrFail($prestamo->idEmpleadoSolicitante);

        $unidad= $empleado->plazaFuncional->unidad;

        $data['prestamo'] = $prestamo;
        $data['empleado'] = $empleado;
        $data['unidad'] = $unidad;
        /*se envia un msj de notificacion de aprobacion de solicitud para prestamo*/
        /*Mail::send('emails.prestamoExpediente.confirmarAprobacion',$data,function($msj) use ($data)
        {              
          $msj->subject('Solicitud de Préstamo de Expediente');
          $msj->to('luis.hernandez@medicamentos.gob.sv');
          $msj->bcc('rogelio.menjivar@medicamentos.gob.sv');
        });*/
    } 
    catch(PDOException $e)
    {
        DB::connection('sqlsrv')->rollback();
        return response()->json(['status' => 400, 'message' => "<ul class='text-warning'><li>OCURRIÓ UN ERROR</li></ul>"],200);   
    }
    DB::connection('sqlsrv')->commit(); 
    return response()->json(['status' => 200, 'message' => "Exito"],200);
  }

  public function historialAutorizaciones()
  { 
    $data = ['title'      => ''
        ,'subtitle'     => ''
        ,'breadcrumb'     => [
          ['nom'  =>  'Historial de Autorizaciones', 'url' => route('solicitudes.to.autorizar')],
          ['nom'  =>  '', 'url' => '#']
        
        ]]; 

    $unidades = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.unidades')
          ->select('idUnidad','nombreUnidad')->get();
    $data['unidades'] = $unidades;   
        
    return view('archivo.historialAutorizaciones',$data);
  } 

  public function getHistorialAutorizaciones(Request $request)
  {
    $idunidad = Empleados::findOrFail(Auth::user()->idEmpleado)->plazaFuncional->idUnidad;

    if($idunidad)
    {
      $autorizaciones = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pe')
          ->join('dnm_rrhh_si.RH.empleados as e','pe.idEmpleadoSolicitante','=','e.idEmpleado')
          ->join('dnm_rrhh_si.RH.unidades as u','pe.idUnidadSolicitante','=','u.idUnidad')
          ->join('dnm_ugda_si.PRES.estadoPrestamo as est','pe.estadoPrestamo','=','est.idEstadoPrestamo')        
          ->select('pe.idPrestamo','pe.noRegistroExpediente','pe.nombreExpediente',DB::connection('sqlsrv')->raw('concat(e.nombresEmpleado,\' \',e.apellidosEmpleado) as eSolicita'),'u.nombreUnidad','pe.fechaPrestamo','pe.idEmpleadoSolicitante','pe.idUnidadSolicitante','pe.fechaAutorizacion','pe.estadoPrestamo','est.nombreEstado')
          //->whereNotNull('pe.idEmpleadoAutoriza') 
          ->where('pe.idUnidadAutoriza',$idunidad)
          ->whereNotIn('estadoPrestamo',[3]) /*no se muestran las que aun no se han aprobado*/
          ->where(function ($query) use ($request) {
            if ($request->has('fidproducto')) 
            {
              $query->where('pe.noRegistroExpediente','LIKE','%'.$request->get('fidproducto').'%');
            }
            if ($request->has('fnomproducto')) 
            {
              $query->where('pe.nombreExpediente','LIKE','%'.$request->get('fnomproducto').'%');
            }
            if ($request->has('fesolicita')) 
            {
              $query->where('pe.idEmpleadoSolicitante','=',$request->get('fesolicita'));
            }
            if ($request->has('fusolicita')) 
            {              
              $query->where('pe.idUnidadSolicitante','=',$request->get('fusolicita'));
            }
            if ($request->has('ffecha')) 
            {
              $fecha = date('Y-m-d',strtotime($request->get('ffecha')));
              $query->where('pe.fechaPrestamo','LIKE','%'.$fecha.'%');
            }          

        });

      return Datatables::of($autorizaciones)
        ->editColumn('fechaPrestamo', function ($autorizaciones) {
                    return $autorizaciones->fechaPrestamo ? with(date('d-m-Y',strtotime($autorizaciones->fechaPrestamo))) : '';
        })
        ->editColumn('fechaAutorizacion', function ($autorizaciones) {
                    return $autorizaciones->fechaAutorizacion ? with(date('d-m-Y',strtotime($autorizaciones->fechaAutorizacion))) : '';
        })
        ->addColumn('estado',function($dt){
            if($dt->estadoPrestamo==6)
            {
              return '<p class="text-danger">DENEGADA</p>';
            }
            else
            {
              return $dt->nombreEstado;
            }                              
        })                 
        ->make(true);
    }
  } 

  public function getEmpleadosToSelectize(Request $request)
  {
    $query = ($request->q);

    if(!$query && $query == '') return response()->json(array(), 400);
    
    $data = DB::connection('sqlsrv')->table('dnm_rrhh_si.RH.empleados')
      ->where('nombresEmpleado','LIKE','%'.mb_strtoupper($query).'%')
      ->orWhere('apellidosEmpleado','LIKE','%'.mb_strtoupper($query).'%')
      ->select('idEmpleado','nombresEmpleado','apellidosEmpleado',DB::raw('concat(nombresEmpleado,\' \',apellidosEmpleado) as nombreempleado'))
      ->take(10)->get();  

    return response()->json(array(
      'data'=>$data
    ));
  }



  public function getPrestamoById(Request $request)
  {

      $prestamo = PrestamoExpedientes::where('idPrestamo',$request->param)->first();
     

      if($prestamo) 
      {
        return response()->json(['status' => 200, 'idP' => $prestamo->noRegistroExpediente,'noP'=>$prestamo->nombreExpediente,'prestamo'=>$prestamo->idPrestamo],200);
      }
      else
      {
        return response()->json(['status' => 400, 'message' => "Ocurrió un error"],200);
      }  

  }

  public function historialMisPrestamos()
  {
    $data = ['title'      => ''
        ,'subtitle'     => ''
        ,'breadcrumb'     => [
          ['nom'  =>  'historial mis préstamos', 'url' => route('misprestamos.historial')],
          ['nom'  =>  '', 'url' => '#']
        
        ]]; 
    $estados = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.estadoPrestamo')
        ->whereIn('idEstadoPrestamo',[5,6,7,9,10])
        ->select('idEstadoPrestamo','nombreEstado')
        ->orderBy('nombreEstado','ASC')
        ->get();
    $data['estados']=$estados;  
        
    return view('archivo.historialMisPrestamos.index',$data);

  }

  public function getMisSolicitudesPrestamos()
  {

    $miusuario = Auth::user();
      
    $misprestamos = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as pre')
      ->join('dnm_ugda_si.PRES.estadoPrestamo as est','pre.estadoPrestamo','=','est.idEstadoPrestamo')
      ->join('dnm_rrhh_si.RH.unidades as u','pre.idUnidadAutoriza','=','u.idUnidad')
      ->leftjoin('dnm_rrhh_si.RH.empleados as ea','pre.idEmpleadoAutoriza','=','ea.idEmpleado')
      ->where('pre.idEmpleadoSolicitante',$miusuario->idEmpleado)
      ->whereIn('pre.estadoPrestamo',[1,2,3,4,8])
      ->select('pre.idPrestamo','pre.noRegistroExpediente','pre.nombreExpediente','pre.estadoPrestamo','est.nombreEstado','pre.fechaPrestamo','u.nombreUnidad as uautoriza',DB::raw('concat(ea.nombresEmpleado,\' \',ea.apellidosEmpleado) as empleadoAutoriza'),'pre.solicitudTransferida');     
      

    return Datatables::of($misprestamos)
    ->editColumn('fechaPrestamo', function ($prestamos) {
                  return $prestamos->fechaPrestamo ? with(date('d-m-Y',strtotime($prestamos->fechaPrestamo))) : '';
    })
    ->editColumn('nombreEstado', function ($prestamos) {
        if($prestamos->estadoPrestamo==6)
        {
          return '<p class="text-danger">'.$prestamos->nombreEstado.'</p>';
        }
        else
        {
          return $prestamos->nombreEstado;
        }
    })   
    ->addColumn('accion',function($dt){
      if($dt->estadoPrestamo==1||$dt->estadoPrestamo==3||$dt->estadoPrestamo==4)
      {
        return '<a onclick="desistirSolicitud(\''.$dt->idPrestamo.'\');" title="enviar a archivo" class="btn btn-xs btn-warning vertical" style="height: 20px;"><p style="font-size:10px;">DESISTIR</p></i></a>';   
      }
      elseif($dt->estadoPrestamo==8)
      {
        return '<input type="checkbox" value="'.$dt->idPrestamo.'" name="listaPrestamos"  class="checkbox2" style="margin-left: 40%;">';
      }
      elseif($dt->estadoPrestamo==2)
      {
        return '<a onclick="fcnretornar(\''.$dt->idPrestamo.'\');" class="btn btn-success btn-xs vertical">Retornar</a>'.
          '<a onclick="fcntransferir(\''.$dt->idPrestamo.'\');" class="btn btn-info btn-xs vertical">Transferir</a>';
      }                 
    })
    ->addColumn('transfirio',function($dt){
      $transfirio = DB::connection('sqlsrv')->table('dnm_ugda_si.PRES.prestamo_expedientes as p')
            ->join('dnm_rrhh_si.RH.empleados as e','p.idEmpleadoSolicitante','=','e.idEmpleado')
            ->where('p.idPrestamo',$dt->solicitudTransferida)
            ->select(DB::raw('concat(e.nombresEmpleado,\' \',e.apellidosEmpleado) as etransfirio'))
            ->first();
      if($transfirio)
      {
        return $transfirio->etransfirio;
      }
                        
    })           
    ->make(true);

  }

  public function desisitirSolicitudPrestamo(Request $request)
  {
    $prestamo = PrestamoExpedientes::where('idPrestamo',$request->param)->first();
    if($prestamo)
    { 
      $prestamo->estadoPrestamo = 10;      
      $prestamo->idUsuarioModifica = Auth::user()->idUsuario;                                    
      $prestamo->save(); 

      bitacoraDePrestamo::insertarBitacora($prestamo->idPrestamo,$prestamo->estadoPrestamo,Auth::user()->idUsuario.'@'.$request->ip(),'SOLICITUD DESISTIDA'); 

      return response()->json(['status' => 200, 'message' => "exito"],200);
    }
    else
    {
      return response()->json(['status' => 400, 'message' => "Ocurrió un error al recuperar el préstamo"],200);
    }

  }
      
}
