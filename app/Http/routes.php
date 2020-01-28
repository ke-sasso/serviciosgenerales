<?php
/*
	This method include all Routes archives from Routes Directory
	By Kevin Alvarez
*/
use App\CatVehiculo;
use App\Trp_Solicitud;
foreach (new DirectoryIterator(__DIR__.'/Routes') as $file)
{
    if (!$file->isDot() && !$file->isDir() && $file->getFilename() != '.gitignore')
    {
        require_once __DIR__.'/Routes/'.$file->getFilename();
        //require_once __DIR__.'/Routes/'.$file->getFilename();
    }
}
/*
	Rutas para Logue en aplicación
	By Kevin Alvarez
*/
Route::get('/', ['as' => 'doLogin', 'uses' => 'CustomAuthController@getLogin']);   
Route::post('/login', 'CustomAuthController@postLogin'); 
Route::get('/login', 'InicioController@index'); 
Route::get('/logout', 'CustomAuthController@getLogout'); 


Route::get('/solautorizacion/{idTipo}/{idSolicitud}/{idEstado}',[
			'as' => 'correo',
			'uses' => 'CustomAuthController@autorizacion2'
	 	]);

/*autorizar o denegar prestamo expediente*/
Route::get('/autorizacion/pexpediente/{idsolicitud}/{idempleado}/{idestado}',[
			'as' => 'correosolpreexppro',			
			'uses' => 'Archivo\PrestamosController@AutorizarPrestamo'
]);
		
Route::get('/inicio',['middleware' => ['auth' , 'verifypermissions'],'permissions' => [442],'as' => 'doInicio', 'uses' => 'InicioController@index']);

/*
	Rutas para cambiar configuraciones del sistema
	By Kevin Alvarez
*/

Route::get('/information/create/ajax-state',function()
{	

    $idmoto = Input::get('idMotorista');
    $vehiculos = CatVehiculo::where('idMotorista',$idmoto)->get();
    return $vehiculos;
 
});



Route::get('cfg/menu', 'InicioController@cfgHideMenu');


Route::post('/empleado/marcacion',array('uses'=>'empleadosController@index'));