<?php namespace App\Models\sim;

use Illuminate\Database\Eloquent\Model;

class sim_productos extends Model {

	protected $table = 'sim.sim_productos';
	protected $primaryKey = 'id_producto';
	public $timestamps = false;
	protected $connection = 'mysql';

}
