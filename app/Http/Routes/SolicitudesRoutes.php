<?php
Route::group(['prefix' => 'solicitudes' , 'middleware' => ['auth' , 'verifypermissions']], function(){
	/*
			Ruta para proceso de establecimientos
	*/
	Route::group(['prefix' => 'transporte'], function(){
		/*
			ADMINISTRADOR
		*/

		Route::get('/',[
			'as' => 'solicitudes.est',
			'permissions' => [442],
			'uses' => 'TransporteController@index'
	 	]);	
		
		Route::get('/solicitudes/unidad',[
			'as' => 'solicitudes.unidad.transporte',
			'permissions' => [450],
			'uses' => 'TransporteController@solicitudesUnidad'
	 	]);	
		
	 	Route::get('/dt-row-data',[
	 		'as' => 'dt.row.data.solicitudes.est',
			'permissions' => [443],
			'uses' => 'TransporteController@anyData'
	 	]);	

	 	Route::get('/nuevasolicitud',[
	 		'as' => 'nuevasolicitud',
			'permissions' => [444],
			'uses' => 'TransporteController@getNuevaSolicitud'
	 	]);
	 	Route::get('/editar/{idSoliTrp}',[
		 		'as' => 'transporte.edicionsolicitud',
		 		'permissions' => [443],
				'uses' => 'TransporteController@edit'
		 	]);	
			
			
		
		Route::get('/asignarkms/{idSoliTrp}',[
		 		'as' => 'transporte.asignarkms',
		 		'permissions' => [443],
				'uses' => 'TransporteController@asignarKms'
		 	]);
			
	 	Route::get('/asignarvehiculo/{idSoliTrp}',[
		 		'as' => 'transporte.asignarvehiculo',
		 		'permissions' => [443],
				'uses' => 'TransporteController@edit'
		 	]);	
	 	Route::get('/cronograma',[
		 		'as' => 'transporte.cronograma',
		 		'permissions' => [445],
				'uses' => 'TransporteController@showCronograma'
		 	]);	
	 	Route::get('/eventos',[
		 		'as' => 'transporte.eventos',
		 		'permissions' => [443],
				'uses' => 'TransporteController@getEvents'
		 	]);
	 	Route::post('/informacion',[
		 		'as' => 'transporte.informacion',
		 		'permissions' => [443],
				'uses' => 'TransporteController@getInformacionEvents'
		 	]);
	 	Route::post('detallep',[
	 			'as'   => 'detallep.destroy',
				'permissions' => [443],
				'uses' => 'DetallePerController@destroy'
				]);
	 	


	 	/*
			CONFIRMACIONES
	 	*/
	 	Route::group(['prefix' => 'confirmaciones'], function(){
			
			Route::get('/cambiarestado/{idEstado},{idSoliTrp}',[
		 		'as' => 'transporte.cambiarestado',
				'permissions' => [443],
				'uses' => 'TransporteController@changeEstado'
		 	]);	
		 	
		 	Route::post('/update',[
		 		'as' => 'transporte.updatesolicitud',
				'permissions' => [447],
				'uses' => 'TransporteController@update'
		 	]);
		 	
		 	Route::post('/guardar',[
		 		'as' => 'solicitudes.est.confirmaciones.guardar',
				'permissions' => [443],
				'uses' => 'TransporteController@store'
		 	]);	
		 	
		 	Route::post('/asignar',[
		 		'as' => 'transporte.asignar',
				'permissions' => [443],
				'uses' => 'TransporteController@asignarVehiculo'
		 	]);
		});
	});	
});
