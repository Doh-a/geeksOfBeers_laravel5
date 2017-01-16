<?php

namespace App\Models;

use Eloquent;

class Country extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'countries';
	
	protected $primaryKey = 'id_country';

}
