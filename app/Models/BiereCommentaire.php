<?php

namespace App\Models;

use Eloquent;

class BiereCommentaire extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere_commentaires';

	protected $primaryKey = 'id_biere_commentaire';
}

?>