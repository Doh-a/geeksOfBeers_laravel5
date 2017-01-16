<?php

namespace App\Models;

use Eloquent;

class EventsLike extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'events_like';

	protected $primaryKey = 'id_like';
}

?>