<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Input;
use View;

use App\Models\Biere;
use App\Models\Brasserie;
use App\Models\Couleur;
use App\Models\Country;
use App\Models\Fermentation;
use App\Models\Maltage;
use App\Models\TypeAmericain;
use App\Models\TypeBelge;
use App\Models\UserBiere;

class BiereController extends BaseController
{

	/**
     * Return all the beers brewed by this brewery
     */
    public function biereDatasAsText($biere_id)
    {
        $biere = Biere::find($biere_id); 
		
		$couleur = Couleur::find($biere->couleur)->first();
		   
		return View::make('brasserie/brasserie', array('bieres' => $bieresShortView, 'brasserie' => $brasserie));
    }
	
	public function addForm()
	{
		//Beers datas
		$brasseries = Brasserie::orderBy('nom_brasserie', 'asc')->lists('nom_brasserie', 'id_brasserie');
		$couleurs = Couleur::orderBy('nom_couleur', 'asc')->lists('nom_couleur', 'id_couleur');
		$fermentations = Fermentation::orderBy('nom_fermentation', 'asc')->lists('nom_fermentation', 'id_fermentation');
		$maltages = Maltage::orderBy('nom_maltage', 'asc')->lists('nom_maltage', 'id_maltage');
		$typesAmericain = TypeAmericain::orderBy('nom_type', 'asc')->lists('nom_type', 'id_type');
		$typesBelge = TypeBelge::orderBy('nom_type2', 'asc')->lists('nom_type2', 'id_type2');
	
		$brasseries->prepend("Nouvelle brasserie", -1);
		$brasseries->prepend("-------------", -2);

		//breweries datas
		$countries = Country::orderBy('country_name', 'asc')->lists('country_name', 'id_country');
	
		//Ok lets send of all this
		return View::make('biere/add', array('brasseriesList' => $brasseries, 'couleursList' => $couleurs, 'fermentationsList' => $fermentations, 'maltagesList' => $maltages, 'typesAmericainList' => $typesAmericain, 'typesBelgeList' => $typesBelge, 'countriesList' => $countries));
	}
	
	public function addBiere()
	{
		//TODO : traiter les cas o� des champs obligatoires n'ont pas �t� remplis
		
		//TODO : ajouter la biere et s'assurer que le cas est bien traite.
		$newBrasserie = false;
		if(Input::get('addBrasserie') == "true")
		{
			$brasserie = new Brasserie;
			
			$brasserie->nom_brasserie = Input::get('brewerie_name');
			$brasserie->country = Input::get('country');
			$brasserie->description_brasserie = Input::get('brewerie_description');
			
			$brasserie->save();
			
			$newBrasserie = $brasserie->id_brasserie;
		}
		
		$biere = new Biere;
		
		//TODO : verifier token et format des donnees
		$biere->nom_biere = Input::get('beer_name');
		$biere->degre = Input::get('beer_degres');
		if($newBrasserie != false)
			$biere->brasserie = $newBrasserie;
		else
			$biere->brasserie = Input::get('brasserie');
		$biere->couleur = Input::get('couleur');
		$biere->fermentation = Input::get('fermentation');
		$biere->maltage = Input::get('maltage');
		$biere->type = Input::get('typeAmericain');
		$biere->type2 = Input::get('typeBelge');
		
		$biere->save();

		//If a file has been uploaded, move it to the good directory
		$tmpEtiquetteName = Input::get('etiquetteFile');
		if($tmpEtiquetteName != 'false')
		{
			$beerImagesPath = "assets/img/bieres/".$biere->id_biere;
			$tmpEtiquettePath = 'files/' . $tmpEtiquetteName;
			$tmpEtiquetteThumbnailPath = 'files/thumbnail/' . $tmpEtiquetteName;
			if (mkdir($beerImagesPath, 0777, true))
			{
				//Move the image
				rename($tmpEtiquettePath, $beerImagesPath.'/'.$tmpEtiquetteName);

				//Create a dir for thumbnails:
				mkdir($beerImagesPath."/thumbnail", 0777, true);
				rename($tmpEtiquetteThumbnailPath, $beerImagesPath."/thumbnail".'/'.$tmpEtiquetteName);
			}
		}

		$biere->etiquette = $biere->id_biere.'/'.$tmpEtiquetteName;
		$biere->save();
		
		//TODO : rediriger vers la page biere avec une confirmation de l'ajout
		return \Redirect::route('biere', ['id' => $biere->id_biere]);

	}
	
	public function bierePresentation($biere_id)
	{
		$biere = Biere::find($biere_id); 
		
		//Get the user feedback about this beer
		if(Auth::user())
		{
			$biereUser = UserBiere::where("id_user", "=", Auth::user()->getAuthIdentifier())->where("id_biere", "=", $biere_id)->first();
		}
		else
		{
			$biereUser = null;
		}
			
		//Get the community notations about this beer
		$biereRates = DB::table('user_biere')
                     ->select(DB::raw('note_biere, count(*) as rate_count'))
                     ->groupBy('note_biere')
					 ->where('id_biere', '=', $biere_id)
                     ->get();
		
		$biereFinalRates = array(0,0,0,0,0,0);
		$biereFinalPercents = array(0,0,0,0,0,0);
		$totalVotes = 0;
		$totalPoints = 0;
		
		foreach($biereRates as $tmpRate)
		{
			$biereFinalRates[$tmpRate->note_biere] += $tmpRate->rate_count;
			$totalVotes += $tmpRate->rate_count;
			$totalPoints += $tmpRate->rate_count * $tmpRate->note_biere;
		}
		
		for($i = 0; $i < 5; $i++)
			if($totalVotes > 0)
				$biereFinalPercents[$i+1] = round($biereFinalRates[$i+1] / $totalVotes * 100);
		
		$biereAverageRate = 0;
		if($totalVotes > 0)
			$biereAverageRate = round($totalPoints/$totalVotes, 2);
			
		//Get the other beers from the same brewery
		$bieresBrewery = Biere::where('brasserie', '=', $biere->brasserie)
					->where('biere.id_biere', '<>', $biere_id)
					->get();
		$bieresBreweryShortView = array();
		foreach ($bieresBrewery as $tmpBiere) {
			$bieresBreweryShortView[] = View::make('biere/biere_short_view', array('biere' => $tmpBiere));
		}

		return View::make('biere/presentation', array('biere' => $biere, 
													'biereUser' => $biereUser, 
													'biereAverageRate' => $biereAverageRate, 
													'biereTotalVotes' => $totalVotes, 
													'biereRates' => $biereFinalRates, 
													'bierePercents' => $biereFinalPercents,
													'bieresBrewery' => $bieresBreweryShortView));
	}

	//Beers home page, will mostly show stats
	public function getBeersHomePage()
	{
		//Find the 5 best ranked beers:
		$topBeers = DB::table('user_biere')
							->select(DB::raw('user_biere.id_biere, biere.nom_biere, AVG(note_biere) as grade, etiquette'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->groupBy('user_biere.id_biere')
							->orderBy('grade', 'desc')
							->limit(5)
							->get();

		//Find the 5 best ranked blond beers:
		$topBlondBeers = DB::table('user_biere')
							->select(DB::raw('user_biere.id_biere, biere.nom_biere, AVG(note_biere) as grade, etiquette'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('couleur', 1)
							->groupBy('user_biere.id_biere')
							->orderBy('grade', 'desc')
							->limit(5)
							->get();

		//Find the 5 best ranked blond beers:
		$topWhiteBeers = DB::table('user_biere')
							->select(DB::raw('user_biere.id_biere, biere.nom_biere, AVG(note_biere) as grade, etiquette'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('couleur', 2)
							->groupBy('user_biere.id_biere')
							->orderBy('grade', 'desc')
							->limit(5)
							->get();


		//Find the 5 best ranked blond beers:
		$topBlackBeers = DB::table('user_biere')
							->select(DB::raw('user_biere.id_biere, biere.nom_biere, AVG(note_biere) as grade, etiquette'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('couleur', 5)
							->groupBy('user_biere.id_biere')
							->orderBy('grade', 'desc')
							->limit(5)
							->get();

		
		return View::make('biere/homePage', array('topBeers' => $topBeers,
													'topBlondBeers' => $topBlondBeers,
													'topWhiteBeers' => $topWhiteBeers,
													'topBlackBeers' => $topBlackBeers
		));
	}

	public function beerSearch($searchKey)
	{
		$beers = Biere::select(DB::raw('id_biere, nom_biere, etiquette, nom_brasserie'))
					->join('brasserie', 'biere.brasserie', '=', 'brasserie.id_brasserie')
					->whereRaw('LOWER(nom_biere) LIKE ?',  array('%' . $searchKey . '%'))
					->get();
		
		$idBeersList = array();
		foreach($beers as $tmpBeer)
		{
			$jsonResult[] = array("id_biere" => $tmpBeer->id_biere, "nom_biere" => $tmpBeer->nom_biere, "etiquette" => $tmpBeer->etiquette, "nom_brasserie" => $tmpBeer->nom_brasserie);
			$idBeersList[] = $tmpBeer->id_biere;
		}
		
		return Response::json( $jsonResult );
	}
}

?>
