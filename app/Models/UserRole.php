<?php

namespace App\Models;

use Eloquent;

class UserRole extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_role';

	protected $primaryKey = 'role_id';
}

?>