<?php 
	Route::group(['prefix' => 'user'], function() {
	    Route::get('passwd', ['as' => 'view.passwd','uses' => 'empleadosController@vCambioPasswd']);

	    Route::post('validatePwd', ['as' => 'validate.passwd','uses' => 'empleadosController@checkPasswd']);

	    Route::post('cambiarPwd',['as' => 'cambiar.passwd','uses'=>'empleadosController@cambiarPasswd']);
	});

 ?>