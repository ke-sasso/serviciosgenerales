<?php namespace App\Models\sim;

use Illuminate\Database\Eloquent\Model;

class sim_usuarios extends Model {

	protected $table = 'sim.sim_usuarios';
	protected $primaryKey = 'id_usuario';
	public $timestamps = false;
	protected $connection = 'transporte';

}
