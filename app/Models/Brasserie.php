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
		$bieres = Biere::where('brasserie', '=', $this->id_brasserie)->get();

		return $bieres;
	}

	public function getBeersCount()
	{
		$beersCount = Biere::where('brasserie', '=', $this->id_brasserie)->count();

		return $beersCount;
	}

	public function country()
	{
		if($this->country == "")
			return "null";

		$country = Country::find($this->country);

		return $country;
	}

	public function creator()
	{
		if($this->created_by == 0)
		{
			$anonymousUser = new User();
			$anonymousUser->username = "Anonymous";
			return $anonymousUser;
		}

		$user = User::find($this->created_by);
		
		return $user;
	}
}
