<?php

namespace App\Models;

use Eloquent;

class UserBrasserie extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_brasserie';

	protected $primaryKey = 'user_brasserie_id';
}

?>