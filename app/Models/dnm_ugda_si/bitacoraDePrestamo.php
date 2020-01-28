<?php namespace App\Models\dnm_ugda_si;

use Illuminate\Database\Eloquent\Model;

class bitacoraDePrestamo extends Model {

	protected $table = 'dnm_ugda_si.PRES.bitacoraDePrestamo';
    protected $primaryKey = 'idActividad';    
    protected $connection = 'sqlsrv';
	const CREATED_AT = 'fechaCreacion';
	const UPDATED_AT = 'fechaModificacion';

	public static function insertarBitacora($idPrestamo,$estado,$usuario,$obs)
	{
		$bitacora = new bitacoraDePrestamo();
        $bitacora->idPrestamo = $idPrestamo; 
        $bitacora->estadoPrestamo = $estado;
        $bitacora->idUsuarioCrea = $usuario;
        $bitacora->observacion = $obs;
        //$bitacora->fechaCreacion = date('Y-m-d H:i:s');
        $bitacora->save();
	}

    public function getDateFormat(){
        return 'Y-m-d H:i:s';
    }	

}
