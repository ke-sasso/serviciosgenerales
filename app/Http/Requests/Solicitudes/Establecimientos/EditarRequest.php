<?php namespace App\Http\Requests\Solicitudes\Establecimientos;

use App\Http\Requests\Request;

use Auth;
use App\UserOptions;

class EditarRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return (Auth::check() && UserOptions::vrfOpt(401))?true:false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'txtIdSoliIns'			=> 'required',
			'txtFechaRecepcion' 	=> 'date',
			'txtFechaProbable'		=> 'required|date',
			'cmbTipo'				=> 'required|numeric',
			'cmbTipoProcedimiento'	=> 'required|numeric',
			'cmbInspectores'		=> 'required|array|min:1'
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
    		'txtIdSoliIns'			=> 'Id Solicitud Inspecciones',
        	'txtFechaRecepcion' 	=> 'Fecha recepción',
			'txtFechaProbable'		=> 'Fecha probable inspección',
			'cmbTipo'				=> 'Tipo solicitud',
			'cmbTipoProcedimiento'	=> 'Tipo procedimiento',
			'cmbInspectores'		=> 'Inspectores'
    	];
    }

}