<?php

namespace App\Models;

use Eloquent;

class Brasserie extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'brasserie';
	
	protected $primaryKey = 'id_brasserie';

	
	public function getBieres()
	{
		$bieres = Biere::where('brasserie', '=', $this->id_brasserie)->take(10)->get();
		
		foreach ($bieres as $biere)
		{
			var_dump($biere->nom_biere);
		}
	}
}
