<?php

namespace App\Models;

use Eloquent;

define("EVENT_BEER_GRADE", 1);
define("EVENT_BEER_COMMENT", 2);
define("EVENT_BREWERY_FAN", 3);

class Timeline extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'timeline';
	
	protected $primaryKey = 'id_event';
}
