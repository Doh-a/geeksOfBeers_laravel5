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
		if($this->couleur == "")
			$this->couleur = 1;
		return Couleur::find($this->couleur);
	}
	
	//Get the name of the fermentation :
	public function fermentation()
	{
		if($this->fermentation == "")
			$this->fermentation = 1;
		return Fermentation::find($this->fermentation);
	}
	
	//Get the name of the maltage :
	public function maltage()
	{
		if($this->maltage == "")
			$this->maltage = 1;
		return Maltage::find($this->maltage);
	}
	
	//Get the name of the american classification :
	public function typeAmericain()
	{
		if($this->type == "")
			$this->type = 1;
		return TypeAmericain::find($this->type);
	}
	
	
	//Get the name of the belgian classification :
	public function typeBelge()
	{
		if($this->type2 == "")
			$this->type2 = 1;
		return TypeBelge::find($this->type2);
	}
	
	//Get the name of the american classification :
	public function brasserie()
	{
		return Brasserie::find($this->brasserie);
	}
}
