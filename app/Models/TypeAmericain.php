<?php

namespace App\Models;

use Eloquent;

class TypeAmericain extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere_type';
	
	protected $primaryKey = 'id_type';
}
