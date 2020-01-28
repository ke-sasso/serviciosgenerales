<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class TrpSoliRequest extends Request {

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
			'fechaSolicitudD' 	    => 'required|date',
			'horaInicio'			=> 'required|date_format:H:i A|',
			'horaFin'				=> 'after:horaInicio|date_format:H:i A',
			'lugar'					=> 'required',
			'idEmpleado'			=> 'required'
			
		];
	}

	public function attributes()
	{
    	return[
        	'fechaSolicitudD' 	    => 'Fecha de Solicitud Desde',
			'horaInicio'			=> 'Hora Inicio',
			'lugar'					=> 'Lugar',
			'idEmpleado'			=> 'Personas a Transportar'
    	];
    }
			//
	
	}

