<?php
Route::group(['prefix' => 'perfiles', 'middleware' => ['auth' , 'verifypermissions']], function() {
	Route::get('/puesto',[
		'as' => 'perfiles.puesto',
		'permissions' => [455], 
		'uses' => 'EDC\AdministradorController@showPerfilesPuesto']);

	Route::get('/getEmpleadosPP',[
		'as' => 'dt.empleados.perfilesp',
		'permissions' => [455], 
		'uses' => 'EDC\AdministradorController@getDataRowsPerfilesP']);

	Route::get('/mostrar/emp/{idEmp}',[
		'as' => 'perfiles.puesto.emp',
		'permissions' => [455], 
		'uses' => 'EDC\AdministradorController@showEmpPerfilPuesto']);

	Route::get('/mostrar/emptar/{idEmp}/{idTar}',[
		'as' => 'perfiles.puesto.emp.showTar',
		'permissions' => [455], 
		'uses' => 'EDC\AdministradorController@mostrarTarea']);
});