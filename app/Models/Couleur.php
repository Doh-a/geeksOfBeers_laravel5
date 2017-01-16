<?php

namespace App\Models;

use Eloquent;

class Couleur extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere_couleur';
	
	protected $primaryKey = 'id_couleur';
}
