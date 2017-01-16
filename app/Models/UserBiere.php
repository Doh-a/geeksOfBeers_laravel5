<?php

namespace App\Models;

use Eloquent;

class UserBiere extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_biere';

	protected $primaryKey = 'user_biere_id';
}

?>