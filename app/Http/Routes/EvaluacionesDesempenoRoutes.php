<?php
Route::group(['prefix' => 'edc' , 'middleware' => ['auth' , 'verifypermissions']], function(){
	/*
		RUTAS PARA EL PROCESO DE EVALUACIONES DE DESEMPEÑO
	*/
	Route::group(['prefix' => 'empleado'], function(){
		/*
			ADMINISTRADOR
		*/
		Route::get('/',[
			'as' => 'edc.admin',
			'permissions' => [453],
			'uses' => 'EDC\AdministradorController@index'
	 	]);

	 	Route::get('/PersonalEnPruebas',[
			'as' => 'edc.admin.pruebas',
			'permissions' => [453],
			'uses' => 'EDC\AdministradorController@indexPersonalEnPruebas'
	 	]);	

		Route::get('/{idEva}/{idEmp}',[
			'as' => 'edc.empleado.evaluar',
			'permissions' => [453],
			'uses' => 'EDC\AdministradorController@evaluar'
	 	]);

	 	//Imprimir evaluacion del empleado
	 	Route::get('vistaprevia/{idEva}/{idEmp}',[
			'as' => 'edc.empleado.vistaprevia',
			'permissions' => [453],
			'uses' => 'EDC\AdministradorController@vistaPrevia'
	 	]);

	 	//Imprimir formato en blanco
	 	Route::get('formato/{idEva}/{idEmp}',[
			'as' => 'edc.empleado.formatoEvaluacion',
			'permissions' => [453],
			'uses' => 'EDC\AdministradorController@formatoEvaluacion'
	 	]);

		Route::post('aprobar', [
		    'as' => 'edc.empleado.aprobar',
			'permissions' => [453],
			'uses' => 'EDC\EvaluacionController@aprobar'
		]);

		Route::group(['prefix' => 'tarea'], function(){
			Route::get('/{idRes}/{idTar}',[
				'as' => 'edc.empleado.evaluar.tarea',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@editarTarea'
		 	]);	

		 	Route::get('/{idRes}/{idTar}/mostrar',[
				'as' => 'edc.empleado.evaluar.tarea.mostrar',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@mostrarTarea'
		 	]);	

		 	Route::post('/actualizar',[
				'as' => 'edc.empleado.evaluar.tarea.actualizar',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@updateTarea'
		 	]);	
		});

		Route::group(['prefix' => 'finalizar/eva'], function(){
			Route::get('/{idRes}',[
				'as' => 'edc.finalizar',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@finalizar'
		 	]);	

		 	Route::post('/guardar',[
				'as' => 'edc.finalizar.guardar',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@finalizarGuardar'
		 	]);	
		});

		Route::group(['prefix' => 'personal/evaluacion'], function(){
			Route::get('/{idRes}',[
				'as' => 'edc.empleado.jefe',
				'permissions' => [453],
				'uses' => 'EDC\EvaluacionController@evaPersonalJefe'
		 	]);
		});
	});


	/*
		RUTAS PARA EL HISTORIAL DE EVALUACIONES DE DESEMPEÑO
	*/
	Route::group(['prefix' => 'historial'], function(){
		Route::get('/',[
			'as' => 'edc.historial',
			'permissions' => [453],
			'uses' => 'EDC\HistorialController@index'
	 	]);	

	 	Route::get('/dt-row-data',[
			'as' => 'dt.row.data.edc.historial',
			'permissions' => [453],
			'uses' => 'EDC\HistorialController@getDataRows'
	 	]);	

	 	Route::get('mostrar/{idRes}',[
			'as' => 'edc.historial.mostrar',
			'permissions' => [453],
			'uses' => 'EDC\HistorialController@mostrar'
	 	]);

	 	Route::get('tarea/{idRes}/{idTar}/mostrar',[
				'as' => 'edc.historial.mostrar.tarea',
				'permissions' => [453],
				'uses' => 'EDC\HistorialController@mostrarTarea'
		 	]);	
	});	


	/*
		RUTAS PARA EL HISTORIAL DE EVALUACIONES DE DESEMPEÑO
	*/
	Route::group(['prefix' => 'rh/admin'], function(){
		Route::get('/',[
			'as' => 'edc.rh.admin',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\FinalizadasController@index'
	 	]);	

	 	Route::get('/dt-row-data',[
			'as' => 'dt.row.data.edc.rh.admin',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\FinalizadasController@getDataRows'
	 	]);	

	 	Route::get('mostrar/{idRes}',[
			'as' => 'edc.rh.admin.mostrar',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\FinalizadasController@mostrar'
	 	]);

	 	Route::get('tarea/{idRes}/{idTar}/mostrar',[
				'as' => 'edc.rh.admin.mostrar.tarea',
				'permissions' => [455],
				'uses' => 'EDC\AdminRH\Evaluaciones\FinalizadasController@mostrarTarea'
		 	]);	
	});	
});


