<?php
Route::group(['prefix' => 'archivo', 'middleware' => ['auth' , 'verifypermissions']], function() {

	Route::group(['prefix' => 'prestamo/expedientes', 'middleware' => ['auth' , 'verifypermissions']], function() {

		Route::get('/',[
			'as' => 'archivo.inicio',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@index']);


		Route::group(['prefix' => 'productos', 'middleware' => ['auth' , 'verifypermissions']], function() 
		{

			Route::get('/lista/{num}',[
				'as' => 'arc.prestamo.exp.prod',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@prestamoEProductos'
			]);
			Route::get('get.exp.prod',[
				'as' => 'dt.exp.prod',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@getDtEProductos'
			]);	

			Route::get('prestar',[
				'as' => 'exp.prod.prestar',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@EProductosPrestar'
			]);

			Route::post('save/prestamo',[
				'as' => 'exp.prod.save.prestamo',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@SavePrestamo'
			]);

			/*Route::get('/autorizacion/pexpediente/{idsolicitud}/{idempleado}/{idestado}',[
			'as' => 'correosolpreexppro',
			'permissions' => [],
			'uses' => 'Archivo\PrestamosController@AutorizarPrestamo'
	 		]);*/

	 		

			Route::post('retornar',[
				'as' => 'exp.prod.retornar',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@RetornarArchivo'
			]);

			Route::post('transferir',[
				'as' => 'exp.prod.transferir',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@TransferirExpediente'
			]);

			Route::get('find.empleado',[
				'as' => 'exp.prod.find.empleado',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@GetEmpleadosUnidad'
			]);

			Route::post('/confirmar-recibido',[
				'as' => 'confirmar.recibido.exp',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@ConfirmarRecibido'
			]);

			Route::get('find.registros.productos',[
				'as' => 'find.registros.productos',
				'permissions' => [442], 
				'uses' => 'Archivo\PrestamosController@findRegistrosToSelectize'
			]);			

		});	

		Route::get('misprestamos',[
			'as' => 'exp.prod.misprestamos',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@EProductosMisPrestamos'
		]);

		Route::get('dtmisprestamos',[
			'as' => 'exp.prod.dt.misprestamos',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@GetMisPrestamosEP'
		]);

		Route::get('dtmissolicitudes',[
			'as' => 'dt.mis.solicitudes.prestamos',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@getMisSolicitudesPrestamos'
		]);

		Route::get('autorizar',[
			'as' => 'solicitudes.to.autorizar',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@solicitudesToAutorizar'
		]);

		Route::get('dt.solicitudes.autorizar',[
			'as' => 'dt.solicitudes.to.autorizar',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@getSolicitudesToAutorizar'
		]);

		Route::get('autorizar.desde.sistema',[
			'as' => 'autorizar.desde.sistema',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@AutorizarPrestamoDesdeSG'
		]);

		Route::get('historial/autorizaciones',[
			'as' => 'historial.autorizaciones',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@historialAutorizaciones'
		]);

		Route::get('dt.historial.autorizaciones',[
			'as' => 'dt.historial.autorizaciones',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@getHistorialAutorizaciones'
		]);

		Route::get('find.empleados.to.selectize',[
			'as' => 'find.empleados.to.selectize',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@getEmpleadosToSelectize'
		]);

		Route::get('desistir/solicitud',[
			'as' => 'desistir.solicitud.prestamo',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@desisitirSolicitudPrestamo'
		]);

		Route::get('get.prestamo.by.id',[
			'as' => 'get.prestamo.by.id',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@getPrestamoById'
		]);


		Route::get('historial/misPréstamos',[
			'as' => 'misprestamos.historial',
			'permissions' => [442], 
			'uses' => 'Archivo\PrestamosController@historialMisPrestamos'
		]);

	});	

});
