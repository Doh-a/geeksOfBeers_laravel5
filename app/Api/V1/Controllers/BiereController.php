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
                    'brewery' => $biere->brasserie(),
                    'color' => $biere->couleur(),
                    'fermentation' => $biere->fermentation(),
                    'maltage' => $biere->maltage(),
                    'rate' => $biereAverageRate,
                    'type' => $biere->typeAmericain(),
                    'type2' => $biere->typeBelge(),
                    );
    }

    public function biereUserDatasAsText($biere_id)
    {
		$biere = Biere::find($biere_id);
       
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

        $biereUser = UserBiere::where("id_user", "=", app('Dingo\Api\Auth\Auth')->user()->getAuthIdentifier())->where("id_biere", "=", $biere_id)->first();

        return array('beer' => $biere, 
                    'brewery' => $biere->brasserie(),
                    'color' => $biere->couleur(),
                    'fermentation' => $biere->fermentation(),
                    'maltage' => $biere->maltage(),
                    'rate' => $biereAverageRate,
                    'type' => $biere->typeAmericain(),
                    'type2' => $biere->typeBelge(),
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
        $createBrasserie = true;
        $brasserie = Brasserie::whereRaw('LOWER(nom_brasserie) LIKE ?',  array('%' . strtolower(Input::get('brewery_name')) . '%'))->first();
        if($brasserie != null)
        {
            $createBrasserie = false;
            $brasserieId = $brasserie->id_brasserie;
        }

		if($createBrasserie)
		{
			$brasserie = new Brasserie;
			
			$brasserie->nom_brasserie = Input::get('brewery_name');
			$brasserie->created_by = app('Dingo\Api\Auth\Auth')->user()->getAuthIdentifier();
			$brasserie->save();
			
			$brasserieId = $brasserie->id_brasserie;
		}
		
		$biere = new Biere;
		
		//TODO : verifier token et format des donnees
		$biere->nom_biere = Input::get('beer_name');
		$biere->degre = Input::get('beer_degres');
		$biere->brasserie = $brasserieId;
        $biere->couleur = Couleur::whereRaw('LOWER(nom_couleur) LIKE ?', array('%' . strtolower(Input::get('color')) . '%'))->first()->id_couleur;
        $biere->fermentation = Fermentation::whereRaw('LOWER(nom_fermentation) LIKE ?', array('%' . strtolower(Input::get('fermentation')) . '%'))->first()->id_fermentation;
        $biere->maltage = Maltage::whereRaw('LOWER(nom_maltage) LIKE ?', array('%' . strtolower(Input::get('maltage')) . '%'))->first()->id_maltage;
		$biere->type = TypeAmericain::where('nom_type', 'LIKE', Input::get('type'))->first()->id_type;
		$biere->type2 = TypeBelge::where('nom_type2', 'LIKE', Input::get('type2'))->first()->id_type2;
		
        //Create the beer
        $biere->save();

        //If a file has been uploaded, move it to the good directory
        
		$image = Input::get('etiquetteFile');
        $tmpEtiquetteName = $biere->id_biere . ".png";
		if($tmpEtiquetteName != 'false')
		{
			$beerImagesPath = "assets/img/bieres/".$biere->id_biere;
			$tmpEtiquettePath = 'files/' . $tmpEtiquetteName;
			$tmpEtiquetteThumbnailPath = 'files/thumbnail/' . $tmpEtiquetteName;

			if (mkdir($beerImagesPath, 0777, true))
			{
				//Move the image
                file_put_contents($tmpEtiquettePath, base64_decode($image));
				rename($tmpEtiquettePath, $beerImagesPath.'/'.$tmpEtiquetteName);
			}
		}

		$biere->etiquette = $tmpEtiquetteName;

		//Update the beer to add the label
		$biere->save();
		
		//TODO : rediriger vers la page biere avec une confirmation de l'ajout
		return array("beer" => $biere);

	}
}

?>
