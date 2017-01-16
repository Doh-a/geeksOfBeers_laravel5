<?php

namespace App\Models;

use Eloquent;

class UserFriend extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_friends';

	protected $primaryKey = 'id_userFriend';
}

?>