<?php

namespace App\Models;

use Eloquent;

class Avatar extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_avatar';
	
	protected $primaryKey = 'avatar_id';
}
