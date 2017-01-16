<?php

namespace App\Models;

use Eloquent;

class Fermentation extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere_fermentation';
	
	protected $primaryKey = 'id_fermentation';
}
