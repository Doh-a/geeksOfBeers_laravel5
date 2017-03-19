<?php

namespace App\Http\Controllers;

use DB;
use Response;

use App\Models\Biere;
use App\Models\Brasserie;

class SearchController extends BaseController
{
	public function mainSearch($searchKey)
	{
		//$searchKey = Input::get('searchQuery');
		//$rootUrl = Input::get('rootUrl');
		$jsonResult = array();

		if($searchKey != '')
		{
			//This will contain the results of the search
			
			
			//This will return beers
			$bieres = Biere::select(DB::raw('id_biere, nom_biere, etiquette'))
						->whereRaw('LOWER(nom_biere) LIKE ?',  array('%' . strtolower($searchKey) . '%'))
						->get();
			
			$idBieresList = array();
			foreach($bieres as $tmpBiere)
			{
				$jsonResult[] = array("link_directory" => "biere", "img_folder" => asset("assets/img/bieres"), "id_item" => $tmpBiere->id_biere, "name_item" => $tmpBiere->nom_biere, "img" => $tmpBiere->id_biere . '/' . $tmpBiere->etiquette);
			}
			
			//This will return breweries
			$breweries = Brasserie::select(DB::raw('id_brasserie, nom_brasserie, img'))
						->whereRaw('LOWER(nom_brasserie) LIKE ?',  array('%' . $searchKey . '%'))
						->get();
			
			$idBreweriesList = array();
			foreach($breweries as $tmpBrewerie)
			{
				$jsonResult[] = array("link_directory" => "brasserie", "img_folder" => "brasseries", "id_item" => $tmpBrewerie->id_brasserie, "name_item" => $tmpBrewerie->nom_brasserie, "img" => $tmpBrewerie->img);
			}
		}
		
		return Response::json( $jsonResult );
	}
}

?>
