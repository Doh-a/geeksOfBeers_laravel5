<?php

namespace App\Models;

use Eloquent;

class EventsComment extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'events_comment';

	protected $primaryKey = 'id_comment';
}

?>