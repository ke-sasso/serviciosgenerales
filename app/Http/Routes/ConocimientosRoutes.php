<?php
Route::group(['prefix' => 'dt-conocimiento' , 'middleware' => ['auth' , 'verifypermissions']], function(){
  Route::get('/',[
      'as'=>'dtc.index',
      'permissions' => [455],
      'uses'=>'CapacitacionesConocimientosController@index'
  ]);
  Route::get('getDt',[
      'as'=>'dtc.getDt',
      'permissions' => [455],
      'uses'=>'CapacitacionesConocimientosController@getDt'
  ]);
});
