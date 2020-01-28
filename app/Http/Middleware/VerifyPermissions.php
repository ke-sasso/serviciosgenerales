<?php namespace App\Http\Middleware;

use Closure;
use Session;

use App\UserOptions;

class VerifyPermissions {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$permissions = $this->getPermissions($request);
		$hasPermission = false;

		if (count($permissions)	== 1) {
			$hasPermission = $this->compareOneSinglePermission(array_first($permissions,function($key, $value){return true;}));
		}else{
			$hasPermission = $this->compareMultiplePermission($permissions);
		}
		
		if (!$hasPermission) {
            return view('errors.401');
        }
        return $next($request);
	}

	private function getPermissions($request)
	{
	    $actions = $request->route()->getAction();
	 
	    return $actions['permissions'];
	}

	private function compareOneSinglePermission($one_permission)
	{	
		//$user_permission = Session::get('PERMISOS', []);
		$user_permission = UserOptions::getAutUserOptions();
		if($user_permission)
			return (in_array($one_permission,$user_permission,true))?true:false;
		else
			return false;
	}
	private function compareMultiplePermission($permissions)
	{	
		//$user_permission = Session::get('PERMISOS', []);
		$user_permission = UserOptions::getAutUserOptions();
		if($user_permission){
			$cont = 0;
			foreach ($permissions as $p)
				if(in_array($p,$user_permission,true)) $cont++;	
			return (count($permissions) == $cont)?true:false;
		}
		
		else
			return false;
	}
}
