<?php
Route::group(['prefix' => 'training', 'middleware' => ['auth' , 'verifypermissions']], function() {


	Route::get('/admin',[
		'as' => 'rh.capacitaciones.admin',
		'permissions' => [455], 
		'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@show']);

	Route::get('/getEvaluaciones', [
		'as' => 'edc.evaluaciones.rh',
		'permissions' => [455],
		'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@getEvaluaciones']);

	Route::post('/nuevaCapacitacion', ['as' => 'new.capacitaciones.rh','permissions' => [455],'uses'=>'EDC\AdminRH\Evaluaciones\capacitacionesController@crearNueva']);

	Route::get('/listCapacitaciones',['as' => 'list.capacitaciones.rh','permissions' => [455],'uses'=>'EDC\AdminRH\Evaluaciones\capacitacionesController@listCapacitaciones']);

	Route::get('/detalleCapacitacion/{id}', ['as' => 'det.capacitaciones.rh','permissions' => [455],'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@getDetalleCapacitacion']);

	Route::get('/vwDetalleCapacitacion/{id}', ['as' => 'vw.capacitaciones.rh','permissions' => [455],'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@vwDetalleCapacitacion']);

	Route::get('/evaluacionCapacitacion/{id}', ['as' => 'evaluacion.capacitaciones.rh','permissions' => [455],'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@evaluacionCapacitacion']);

	Route::post('/guardarEvaluacionCap', ['as' => 'store.evaluacion.capacitaciones','permissions' => [455],'uses'=>'EDC\AdminRH\Evaluaciones\capacitacionesController@storeEvaluacionCapacitacion']);
	
});

Route::group(['prefix' => 'emp'], function() {
	Route::get('/training',[ 
		'as' => 'rh.capacitaciones.emp',
		'permissions' => [455], 
		'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@showEmpCapacitaciones']);

    Route::get('capa', ['as' => 'rh.list.capacitaciones.emp','permissions' => [455],'uses' => 'EDC\AdminRH\Evaluaciones\capacitacionesController@getEvaluacionesEmp']);
});
 ?>
