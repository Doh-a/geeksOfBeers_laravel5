<?php

namespace App\Models;

use Eloquent;

class Biere extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'biere';
	
	protected $primaryKey = 'id_biere';
	
	//Get the name of the color :
	public function couleur()
	{
		return Couleur::find($this->couleur);
	}
	
	//Get the name of the fermentation :
	public function fermentation()
	{
		return Fermentation::find($this->fermentation);
	}
	
	//Get the name of the maltage :
	public function maltage()
	{
		return Maltage::find($this->maltage);
	}
	
	//Get the name of the american classification :
	public function typeAmericain()
	{
		return TypeAmericain::find($this->type);
	}
	
	
	//Get the name of the belgian classification :
	public function typeBelge()
	{
		return TypeBelge::find($this->type2);
	}
	
	//Get the name of the american classification :
	public function brasserie()
	{
		return Brasserie::find($this->brasserie);
	}
}
