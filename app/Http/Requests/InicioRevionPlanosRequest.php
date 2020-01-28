<?php namespace App\Http\Requests;

use Auth;

use App\Http\Requests\Request;

class InicioRevionPlanosRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
		
			'cmbTipoLab' 			=> 'required',
			'cmbTipoTra'			=> 'required',
			'txtIdSoliInspeccion'	=> 'required'
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
        	'cmbTipoLab' 			=> 'Tipo Laboratorio',
			'cmbTipoTra' 			=> 'Tipo Tramite',
			'txtIdSoliInspeccion'	=> 'Id solicitud inspección'
    	];
    }

}
