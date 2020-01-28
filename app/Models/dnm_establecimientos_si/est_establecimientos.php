<?php namespace App\Models\dnm_establecimientos_si;

use Illuminate\Database\Eloquent\Model;

class est_establecimientos extends Model {

	protected $table = 'dnm_establecimientos_si.est_establecimientos';
	protected $primaryKey = 'idEstablecimiento';
	public $timestamps = false;
	protected $connection = 'mysql';

}
