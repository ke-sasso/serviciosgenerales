<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

class UserOptions extends Model {

	protected $table = 'dnm_catalogos.sys_usuario_roles';
	protected $primaryKey = ['codUsuario','codOpcion'];
	protected $timestap = false;

	public static function vrfOpt($id_opcion){
		if (UserOptions::where('codUsuario',Auth::user()->idUsuario)->where('codOpcion',$id_opcion)->count() > 0)
			return true;
		else
			return false;
	}

	public static function verifyOption($id_usuario,$id_opcion){
		if (UserOptions::where('codUsuario',$id_usuario)->where('codOpcion',$id_opcion)->count() > 0)
			return true;
		else
			return false;
	}

	public static function getAutUserOptions(){
		return UserOptions::join('dnm_catalogos.sys_opciones','codOpcion','=','idOpcion')
		->whereIn('idPerfil',[10,1,0])->where('codUsuario',Auth::user()->idUsuario)->select('codOpcion')->lists('codOpcion');
	}


}
