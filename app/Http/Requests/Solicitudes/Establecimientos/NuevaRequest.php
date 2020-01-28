<?php namespace App\Http\Requests\Solicitudes\Establecimientos;

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
		return (Auth::check() && UserOptions::vrfOpt(400))?true:false;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [	
			'txtFechaRecepcion' 	=> 'date',
			'txtFechaProbable'		=> 'required|date',
			'cmbTipo'				=> 'required|numeric',
			'cmbTipoProcedimiento'	=> 'required|numeric',
			'cmbInspectores'		=> 'required|array|min:1',
			'txtIdSoliEst'			=> 'required'
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
        	'txtFechaRecepcion' 	=> 'Fecha recepción',
			'txtFechaProbable'		=> 'Fecha probable inspección',
			'cmbTipo'				=> 'Tipo solicitud',
			'cmbTipoProcedimiento'	=> 'Tipo procedimiento',
			'cmbInspectores'		=> 'Inspectores',
			'txtIdSoliEst'			=> 'Id Solicitud Establecimiento'
    	];
    }
    
}