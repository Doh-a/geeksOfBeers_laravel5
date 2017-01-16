<?php

namespace App\Models;

use Eloquent;

class Maltage extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere_maltage';
	
	protected $primaryKey = 'id_maltage';
}
