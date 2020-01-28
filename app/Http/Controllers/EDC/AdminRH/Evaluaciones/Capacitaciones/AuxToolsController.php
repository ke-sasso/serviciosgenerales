<?php namespace App\Http\Controllers\EDC\AdminRH\Evaluaciones\Capacitaciones;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB;
use Auth;
use Crypt;
use Validator;

use App\Models\rrhh\edc\CapacitacionesDetalle;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuxToolsController extends Controller {
	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('auth');
	}

	public function getCmbCapacitaciones(){

	}

	public function addDetCapacitaciones(Request $request){
		 $rules = [
            'capacitacion' 	=>  'required|numeric',
            'idResDet'		=>	'required|array|min:1',
            'tipo'			=>	'required|numeric|min:1|max:4'
        ];

        $v = Validator::make($request->all(),$rules);
        //Validaciones de sistema
        $v->setAttributeNames([
            'capacitacion'	=>	'Capacitación',
            'idResDet'		=>	'Detalle items',
            'tipo'			=>	'Tipo evaluado'
        ]);

        if ($v->fails()){
            $msg = "<ul>";
            foreach ($v->messages()->all() as $err) {
                $msg .= "<li>$err</li>";
            }
            $msg .= "</ul>";
            return response()->json(['status' => 400, 'message' => $msg]);
        }

        /*
         *      ADICIÓN DE DETALLES
         */
        DB::beginTransaction();
        try {
        	$usuario = Auth::user()->idUsuario.'@'.$request->ip();
        	$detName = "";
        	$routeToRedirect = "";
        	switch ($request->tipo) {
        		case '1':
        			$detName = "idDesempenio";
        			$routeToRedirect = "rh.capacitaciones.desempenios";
        			break;
        		case '2':
        			$detName = "idProducto";
        			$routeToRedirect = "rh.capacitaciones.productos";
        			break;
        		case '3':
        			$detName = "idTipoConocimiento";
        			$routeToRedirect = "rh.capacitaciones.conocimientos";
        			break;
        		case '4':
        			$detName = "idTipoActitud";
        			$routeToRedirect = "rh.capacitaciones.actitudes";
        		default:
        			break;
        	}

        	foreach ($request->idResDet as $key => $value) {
        		list($resultado,$detVal) = explode('~', $value);
        		//$data = ['idCapacitacion' => $request->capacitacion, 'idResultado' =>  $resultado,$detName => $detVal];
        		$capaDet = CapacitacionesDetalle::where('idCapacitacion',$request->capacitacion)
        			->where('idResultado',$resultado)->where($detName,$detVal)->first();
        		if(empty($capaDet)){
        			$data['idUsuarioCreacion'] = $usuario;
        			$capaDet = new CapacitacionesDetalle;
        			$capaDet->idCapacitacion = $request->capacitacion;
        			$capaDet->idResultado = $resultado;
        			$capaDet->$detName = $detVal;
        			$capaDet->idUsuarioCreacion = $usuario;
        			$capaDet->save();
        		}else{
        			$capaDet->idUsuarioModificacion = $usuario;
        			$capaDet->save();
        		}
        	}        	
        } catch(Exception $e){
            DB::rollback();
            throw $e;
            return $e->getMessage();
        }  
        DB::commit();
        return response()->json(['status' => 200, 'message' => "Registros añadidos exitosamente a la capacitación!", "redirect" => route($routeToRedirect)]);
	}
}