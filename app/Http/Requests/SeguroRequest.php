<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class SeguroRequest extends Request {

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
			'presentado' 	    => 'required|min:1',
			'motivo'			=> 'required',
			'det1'				=> 'required',
			'det2'			    => 'required',
			'file'			    => 'required'			
		];
	}

	public function attributes()
	{
    	return[
        	'presentado' 	    => 'Presentado por',
			'motivo'			=> 'Es necesario que seleccione un motivo del reintegro',
			'det1'				=> 'Es necesario que conteste a la pregunta: ¿Resulta la dolencia de la ocupación del asegurado?',
			'det2'			    => 'Es necesario que conteste a la pregunta: ¿Fue tratado anteriormente por esta dolencia?',
			'file'			    => 'Es necesario que suba un archivo pdf',
    	];
    }

}
