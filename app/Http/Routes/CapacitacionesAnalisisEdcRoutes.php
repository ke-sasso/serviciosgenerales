<?php 

Route::group(['prefix' => 'training/plan', 'middleware' => ['auth' , 'verifypermissions']], function() {

	/*
	 *		INDEX
	 */

	Route::get('/',[
		'as' => 'rh.capacitaciones.plan',
		'permissions' => [455], 
		'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@index']);
	
	/*
	 *		RUTAS PARA DESEMPEÑOS
	 */
    
    Route::group(['prefix' => 'desempenios'], function(){
    	Route::get('/',[
			'as' => 'rh.capacitaciones.desempenios',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\DesempeniosController@index']);

		Route::get('/dt-row-data',[
			'as' => 'dt.row.data.rh.capacitaciones.desempenios',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\DesempeniosController@getDataRows']);	

		Route::get('export/excel',[
			'as' => 'rh.capacitaciones.desempenios.expExcel',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\DesempeniosController@exportToExcel']);
    });

    /*
	 *		RUTAS PARA PRODUCTOS
	 */

	Route::group(['prefix' => 'productos'], function(){
		Route::get('/',[
			'as' => 'rh.capacitaciones.productos',
			'permissions' => [455], 
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ProductosController@index']);

		Route::get('/dt-row-data',[
			'as' => 'dt.row.data.rh.capacitaciones.productos',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ProductosController@getDataRows']);

		Route::get('export/excel',[
			'as' => 'rh.capacitaciones.productos.expExcel',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ProductosController@exportToExcel']);
	});

	/*
	 *		RUTAS PARA CONOCIMIENTOS
	 */

	Route::group(['prefix' => 'conocimientos'], function(){
		Route::get('/',[
			'as' => 'rh.capacitaciones.conocimientos',
			'permissions' => [455], 
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ConocimientosController@index']);

		Route::get('/dt-row-data',[
			'as' => 'dt.row.data.rh.capacitaciones.conocimientos',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ConocimientosController@getDataRows']);

		Route::get('export/excel',[
			'as' => 'rh.capacitaciones.conocimientos.expExcel',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ConocimientosController@exportToExcel']);
	});

	/*
	 *		RUTAS PARA CONOCIMIENTOS
	 */

	Route::group(['prefix' => 'actitudes'], function(){
		Route::get('/',[
			'as' => 'rh.capacitaciones.actitudes',
			'permissions' => [455], 
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ActitudesController@index']);

		Route::get('/dt-row-data',[
			'as' => 'dt.row.data.rh.capacitaciones.actitudes',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ActitudesController@getDataRows']);

		Route::get('export/excel',[
			'as' => 'rh.capacitaciones.actitudes.expExcel',
			'permissions' => [455],
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\ActitudesController@exportToExcel']);
	});

	/*
	 *		RUTAS PARA GUARDAR DETALLES SELECCIONADOS
	 */

	Route::group(['prefix' => 'items'], function(){
		Route::post('/guardar',[
			'as' => 'rh.capacitaciones.items.guardar',
			'permissions' => [455], 
			'uses' => 'EDC\AdminRH\Evaluaciones\Capacitaciones\AuxToolsController@addDetCapacitaciones']);
	});
});

?>