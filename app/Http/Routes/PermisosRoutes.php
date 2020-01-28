<?php
Route::group(['prefix' => 'permisos' , 'middleware' => ['auth' , 'verifypermissions']], function(){
	/*
			Ruta para proceso de establecimientos
	*/

	Route::post('/actualizar/datos',[
			'as' => 'actualizar.datos',
			'permissions' => [442],
			'uses' => 'InicioController@actualizarDatos'
	 	]);	

	Route::group(['prefix' => 'solicitudes'], function(){
		/*
			ADMINISTRADOR
		*/
		Route::get('/histMarcaciones', [
			'as' => 'histMarcaciones',
			'permissions' => [441],
			'uses' => 'empleadosController@index'
			]);

		Route::post('/histMarcaciones', [
			'as' => 'histMarcaciones',
			'permissions' => [441],
			'uses' => 'empleadosController@index'
		]);

		Route::get('/',[
			'as' => 'nomarcacion',
			'permissions' => [441],
			'uses' => 'PermisosController@getNoMarcacion'
	 	]);	

	 	Route::get('/licencia',[
			'as' => 'licencia',
			'permissions' => [441],
			'uses' => 'PermisosController@getLicencia'
	 	]);	

	 	Route::get('/seguro',[
			'as' => 'seguro',
			'permissions' => [441],
			'uses' => 'PermisosController@getSeguro'
	 	]);	
		

	 	Route::post('/postseguro',[
		 		'as' => 'guardar.seguro',
		 		'permissions' => [441],
				'uses' => 'PermisosController@storeSeguro'
		 	]);
		
		Route::post('/postlicencia',[
		 		'as' => 'guardar.licencia',
		 		'permissions' => [441],
				'uses' => 'PermisosController@storeLicencia'
		 	]);
		
		Route::post('/autorizacion',[
		 		'as' => 'autorizar.permiso',
		 		'permissions' => [441],
				'uses' => 'PermisosController@autorizacion'
		 	]);

		Route::post('/autorizacion/superior',[
		 		'as' => 'autorizar.superior',
		 		'permissions' => [441],
				'uses' => 'PermisosController@autorizacionSuperior'
		 	]);

			
		Route::post('/denegar',[
		 		'as' => 'denegar.solicitud',
		 		'permissions' => [441],
				'uses' => 'PermisosController@denegar'
		 	]);
		
		Route::post('/postnomarcacion',[
		 		'as' => 'guardar.no.marcacion',
		 		'permissions' => [441],
				'uses' => 'PermisosController@storeNoMarcacion'
		 	]);
			
		Route::post('/get-enfermedades',[
		 		'as' => 'get.enfermedades',
		 		'permissions' => [441],
				'uses' => 'PermisosController@getEnfermedades'
		 	]);
			
		Route::get('/todas',[
			'as' => 'all.permisos',
			'permissions' => [441],
			'uses' => 'PermisosController@getSolicitudes'
	 	]);	
	 	Route::get('/todasSeguros',[
			'as' => 'all.seguros.dnm',
			'permissions' => [452],
			'uses' => 'PermisosController@segurosEmpleados'
	 	]);	
	 		Route::get('/dt-row-data-seguros-DNM',[
	 		'as' => 'dt.row.data.seguros.DNM',
			'permissions' => [452],
			'uses' => 'PermisosController@getDataRowsSegurosDNM'
	 	]);
        Route::get('/mostrarSeguroEmpleado/{idSolSeguro}',[
			'as' => 'ver.seguro.empleado',
			'permissions' => [452],
			'uses' => 'PermisosController@mostrarSeguroDNM'
	 	]);

	 	Route::get('/todasDNM',[
			'as' => 'all.permisos.dnm',
			'permissions' => [452],
			'uses' => 'PermisosController@getSolicitudesDNM'
	 	]);	
		
		Route::get('/dt-row-data-soli',[
	 		'as' => 'dt.row.data.permisos',
			'permissions' => [441],
			'uses' => 'PermisosController@getDataRowsSolicitudes'
	 	]);
		
		Route::get('/dt-row-data-soli-dnm',[
	 		'as' => 'dt.row.data.permisos.dnm',
			'permissions' => [452],
			'uses' => 'PermisosController@getDataRowSolicitudesAdmin'
	 	]);

		Route::get('/getSeguros',[
			'as' => 'all.seguros',
			'permissions' => [441],
			'uses' => 'PermisosController@solicitudesSeguro'
	 	]);	
		
		Route::get('/dt-row-data-seguros',[
	 		'as' => 'dt.row.data.seguros',
			'permissions' => [441],
			'uses' => 'PermisosController@getDataRowsSeguros'
	 	]);
		
				
		Route::get('/mostrarSeguro/{idSolSeguro}',[
			'as' => 'ver.seguro',
			'permissions' => [441],
			'uses' => 'PermisosController@mostrarSeguro'
	 	]);
		
		Route::get('/todas/unidad',[
			'as' => 'all.permisos.unidad',
			'permissions' => [450],
			'uses' => 'PermisosController@getSolicitudesByUnidad'
	 	]);
		
		Route::get('/dt-row-data-soli-unidad',[
	 		'as' => 'dt.row.data.permisos.unidad',
			'permissions' => [450],
			'uses' => 'PermisosController@getDataRowsSolicitudesByUnidad'
	 	]);

		Route::post('/bussinessDays',[
	 		'as' => 'calcular.dias',
			'permissions' => [441],
			'uses' => 'PermisosController@verificarDiasPermiso'
	 	]);
	 	
		Route::get('/showSolicitud/{idTipo}/{idSolicitud}',[
		 		'as' => 'ver.solicitud',
		 		'permissions' => [441],
				'uses' => 'PermisosController@mostrarSolicitud'
		 	]);

		Route::post('/desistir',[
		 		'as' => 'desistir.solicitud',
		 		'permissions' => [441],
				'uses' => 'PermisosController@changeEstadoSol'
		 	]);		
	 	
		Route::get('/verDocumento/{urlDocumento}',[
		 		'as' => 'ver.documento',
		 		'permissions' => [441],
				'uses' => 'PermisosController@download'
		 	]);	
			
		});	

		Route::get('/licencias/autorizar',[
			'as' => 'all.licencias.director',
			'permissions' => [441],
			'uses' => 'PermisosController@solicitudesLicenciaDirector'
	 	]);
		
		Route::get('/dt-row-data-licencia-aut',[
	 		'as' => 'dt.row.data.licencia.autorizar',
			'permissions' => [441],
			'uses' => 'PermisosController@getDataRowsLicenciaDirector'
	 	]);

	 	Route::get('/mostrarlicencia/{idTipo}/{idSolicitud}',[
		 		'as' => 'ver.solicitud.autorizar',
		 		'permissions' => [441],
				'uses' => 'PermisosController@mostrarSolicitud'
		 	]);

	 	Route::get('/procesar/{idTipo}/{idSolicitud}',[
		 		'as' => 'procesar.solicitud',
		 		'permissions' => [441],
				'uses' => 'PermisosController@procesarRrhh'
		 	]);	

	 	Route::post('/NoProcesar',[
		 		'as' => 'noprocesar.solicitud',
		 		'permissions' => [441],
				'uses' => 'PermisosController@procesarRrhh'
		 	]);

		Route::get('/correoSolAdm/{idSol}/{tipo}', [
				'as' => 'reenviar.soladm.auth',
				'permissions' => [441],
				'uses' => 'PermisosController@resendEmailAuth'
			]);	
});
