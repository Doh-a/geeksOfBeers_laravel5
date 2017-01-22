<?php

namespace App\Api\V1\Controllers;

use Auth;
use DB;
use Input;

use App\Models\Biere;
use App\Models\Brasserie;
use App\Models\Couleur;
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
        $brasserie = Brasserie::find($biere->brasserie)->first();
		$couleur = Couleur::find($biere->couleur)->first();
        $fermentation = Fermentation::find($biere->fermentation)->first();
        $maltage = Maltage::find($biere->maltage)->first();
        $typeAmericain = TypeAmericain::find($biere->type)->first();
        $typeBelge = TypeBelge::find($biere->type2)->first();

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

        return array('beer' => $biere, 
                    'brewery' => $brasserie,
                    'color' => $couleur,
                    'fermentation' => $fermentation,
                    'maltage' => $maltage,
                    'rate' => $biereAverageRate,
                    'type' => $typeAmericain,
                    'type2' => $typeBelge,
                    );
    }

    public function biereUserDatasAsText($biere_id)
    {
		$biere = Biere::find($biere_id);
        $brasserie = Brasserie::find($biere->brasserie)->first();
		$couleur = Couleur::find($biere->couleur)->first();
        $fermentation = Fermentation::find($biere->fermentation)->first();
        $maltage = Maltage::find($biere->maltage)->first();
        $typeAmericain = TypeAmericain::find($biere->type)->first();
        $typeBelge = TypeBelge::find($biere->type2)->first();

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

        $biereUser = UserBiere::where("id_user", "=", Auth::user()->getAuthIdentifier())->where("id_biere", "=", $biere_id)->first();

        return array('beer' => $biere, 
                    'brewery' => $brasserie,
                    'color' => $couleur,
                    'fermentation' => $fermentation,
                    'maltage' => $maltage,
                    'rate' => $biereAverageRate,
                    'type' => $typeAmericain,
                    'type2' => $typeBelge,
                    'userRate' => $biereUser
                    );
    }

    public function randomBeers()
    {
        $bieres = Biere::where('etiquette', '!=', "")->orderByRaw("RAND()")->limit(5)->get();

        return array('bieres' => $bieres);
    }

    public function addBeer()
	{
		//Test if the brewery already exists:
        $createBrasserie = false;
        $brasserie = Brasserie::where('nom_brasserie', 'LIKE', Input::get('brewery_name'))->first();
        if($brasserie != null)
        {
            $createBrasserie = false;
            $brasserieId = $brasserie->id_brasserie;
        }

		if($createBrasserie)
		{
			$brasserie = new Brasserie;
			
			$brasserie->nom_brasserie = Input::get('brewerie_name');
			
			$brasserie->save();
			
			$brasserieId = $brasserie->id_brasserie;
		}
		
		$biere = new Biere;
		
		//TODO : verifier token et format des donnees
		$biere->nom_biere = Input::get('beer_name');
		$biere->degre = Input::get('beer_degres');
		$biere->brasserie = $brasserieId;
		$biere->couleur = Couleur::where('nom_couleur', 'LIKE', Input::get('couleur'))->first();
		$biere->fermentation = Fermentation::where('nom_fermentation', 'LIKE', Input::get('fermentation'))->first();
		$biere->maltage = Maltage::where('nom_maltage', 'LIKE', Input::get('maltage'))->first();;
		$biere->type = TypeAmericain::where('nom_type', 'LIKE', Input::get('type'))->first();
		$biere->type2 = TypeBelge::where('nom_type2', 'LIKE', Input::get('type2'))->first();
		
		$biere->save();

		//TODO: add upload a picture
		$biere->save();
		
		//TODO : rediriger vers la page biere avec une confirmation de l'ajout
		return array($biere);

	}
}

?>
