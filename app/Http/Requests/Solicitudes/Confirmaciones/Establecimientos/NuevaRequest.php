<?php namespace App\Http\Requests\Solicitudes\Confirmaciones\Establecimientos;

use App\Http\Requests\Request;

use Auth;
use App\UserOptions;

class NuevaRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return (Auth::check() && UserOptions::vrfOpt(445))?true:false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'txtIdSoliIns'			=> 	'required',
			'cmbEstadoConfirmacion'	=>	'required|numeric'
		];
	}

	/**
	 * Get the validation attributes that apply to the request.
	 *
	 * @return array
	 */
	public function attributes()
	{
    	return[
        	'txtIdSoliIns'			=> 	'Id Solicitud Inspecciones',
        	'cmbEstadoConfirmacion'	=>	'Estado confirmación'
    	];
    }

}
