<?php
Route::group(['prefix' => 'marcaciones' , 'middleware' => ['auth' , 'verifypermissions']], function(){
	/*
			Ruta para proceso de establecimientos
	*/

	Route::get('/getMarcaciones',[
			'as' => 'get.marcaciones',
			'permissions' => [441],
			'uses' => 'empleadosController@getMarcaciones'
	 	]);	

	Route::post('/getMarcacionesByEmpleado',[
			'as' => 'get.marcaciones.by.empleado',
			'permissions' => [441],
			'uses' => 'empleadosController@getMarcacionesByEmpleado'
	 	]);	

	Route::get('/marcionEmpleados',[
			'as' => 'marcacion.empleados',
			'permissions' => [441],
			'uses' => 'empleadosController@marcacionEmpleados'
	 	]);	

		
});
