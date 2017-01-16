<?php

namespace App\Http\Controllers;

use DB;
use Response;

use App\Models\Brasserie;
use App\Models\UserBrasserie;

class BrasserieUserController extends BaseController
{

	 /**
     * Return all the beers of this brewery already tried by this user
     */
    public function bieresTestee($brasserie_id, $user_id)
    {
	   $brasserie = Brasserie::find($brasserie_id); 
		
		$myRates = array();
		$communityRates = array();
		$communityRatesCount = array();
		$beerName = array();
		$beerId = array();
		
		//TODO : faire les notes moyennes des amis �galement
		//Get rates of every one on each beer that the user rated
		$bieresTestees = DB::table('biere')
			->join('user_biere', 'biere.id_biere', '=', 'user_biere.id_biere')
			->where('brasserie', '=', $brasserie_id)
            ->whereExists(function($query) use($user_id)
            {
                $query->select(DB::raw(1))
                      ->from('user_biere')
                      ->whereRaw('user_biere.id_user = 24')
					  ->whereRaw('user_biere.id_biere = biere.id_biere');
            })->get();

		foreach($bieresTestees as $tmpBiereTestee)
		{
			$keyPos = array_search($tmpBiereTestee->id_biere, $beerId);
			if($keyPos === FALSE)
			{
				$beerId[] = $tmpBiereTestee->id_biere;
				$beerName[] = $tmpBiereTestee->nom_biere;
				$keyPos = count($beerId)-1;
				$communityRates[] = $tmpBiereTestee->note_biere;
				$communityRatesCount[] = 1;
			}
			else
			{
				$communityRates[$keyPos] += $tmpBiereTestee->note_biere;
				$communityRatesCount[$keyPos]++;
			}
			
			if($tmpBiereTestee->id_user == $user_id)
				$myRates[] = $tmpBiereTestee->note_biere;
		}
			
			
		//Get all the beers of this brasserie
		$allBieres = DB::table('biere')
			->where('brasserie', '=', $brasserie_id)
			->get();
		
		$bieresTotal = 0;
		
		//Get the amount of rates
		foreach($allBieres as $tmpBiere)
		{
			$bieresTotal++;
			
			$keyPos = array_search($tmpBiere->id_biere, $beerId);
			if($keyPos !== FALSE)
				$communityRates[$keyPos] = round($communityRates[$keyPos] / $communityRatesCount[$keyPos], 2);
		}
		
		//Are we a fan of this brewery ?
		$userBrasserie = UserBrasserie::where('id_brasserie', '=', $brasserie_id)
										->where('id_user', '=', $user_id)
										->first();
		
		$response = array(
            'total' => $bieresTotal,
            'tested' => count($myRates),
			'beersName' => $beerName,
			'beersId' => $beerId,
			'communityRates' => $communityRates,
			'myRates' => $myRates,
			'userBrasserieFan' => ($userBrasserie) ? $userBrasserie->fan : 0,
			'userBrasserieCom' => ($userBrasserie) ? $userBrasserie->commentaire : ''
        );
		   
		return Response::json( $response );
    }

	 /**
     * Change status and return if the fan is a fan or not of this brewery
     */
    public function brasserieFan($brasserie_id, $fanStatus)
    {
		if(Auth::user() != null && ($fanStatus == 0 || $fanStatus == 1))
		{
			//Are we a fan of this brewery ?
			$userBrasserie = UserBrasserie::where('id_brasserie', '=', $brasserie_id)
											->where('id_user', '=', Auth::user()->getAuthIdentifier())
											->first();
			
			$userBrasserie->fan = $fanStatus;
			$userBrasserie->save();
			$eventRefId = $userBrasserie->user_brasserie_id;
			
			//Save this update in the timeline :
			if($eventRefId != -1)
			{
				$newEvent = new Timeline;
				$newEvent->type = EVENT_BREWERY_FAN;
				$newEvent->user1 = Auth::user()->getAuthIdentifier();
				$newEvent->ref_id = $eventRefId;
				$newEvent->save();
			}
			
			return Response::json($userBrasserie->fan);
		}
		else
			return Response::json("You need to be logged in.");
	}
	
	/**
     * Delete comment about this brewery, only for the current user
     */
    public function brasserieDeleteComment($brasserie_id)
    {
		if(Auth::user() != null)
		{
			$userBrasserie = UserBrasserie::where('id_brasserie', '=', $brasserie_id)
											->where('id_user', '=', Auth::user()->getAuthIdentifier())
											->first();
			
			$userBrasserie->commentaire = "";
			$userBrasserie->save();
			
			return Response::json(1);
		}
		else
			return Response::json(0);
	}

	/**
     * Create a comment about this brewery, only for the current user
     */
    public function brasserieNewComment()
    {
		if(Auth::user() != null && Session::token() === Input::get( '_token' ) && Input::get('brewery') != '' ) 
		{
			$userBrasserie = UserBrasserie::where('id_brasserie', '=', Input::get('brewery'))
											->where('id_user', '=', Auth::user()->getAuthIdentifier())
											->first();
			
			if($userBrasserie == null)
			{
				$userBrasserie = new UserBrasserie;
				$userBrasserie->id_user = Auth::user()->getAuthIdentifier();
				$userBrasserie->id_brasserie = Input::get('brewery');
			}
			
			$userBrasserie->commentaire = Input::get('comment');
			$userBrasserie->save();
			
			return Response::json(1);
		}
		else
			return Response::json(0);
	}
	
	/**
	 * Return all the breweries tested by the user, his favorites and a few other stats :
	 */
	public function brasseriesTested($user_id)
	{
		//List all the breweries already tested by the user :
		$knownBreweries = DB::table('biere')
						->select(DB::raw('brasserie.id_brasserie, brasserie.nom_brasserie, brasserie.img, AVG(user_biere.note_biere) as avg_grade'))	
						->join('user_biere', 'user_biere.id_biere', '=', 'biere.id_biere')
						->join('brasserie', 'biere.brasserie', '=', 'brasserie.id_brasserie')
						->where('id_user', '=', Auth::user()->getAuthIdentifier())
						->groupBy('biere.brasserie')
						->orderByRaw('avg_grade desc')
						->get();
		
		$tmpCounter = 0;
		$myFavoritesBreweries = array();
		$myBreweriesIdList = array();
		
		foreach($knownBreweries as $tmpBrewery)
		{
			$myBreweriesIdList[] = $tmpBrewery->id_brasserie;
			if($tmpCounter < 5)
			{
				$myFavoritesBreweries[] = $tmpBrewery;
				$tmpCounter++;
			}
		}
		
		$response = array();
		$response['myTotalBreweries'] = count($knownBreweries);
		$response['bestBreweries'] = $myFavoritesBreweries;
		
		//Find the countries of the breweries tested by the user :
		$myBreweriesByCountrySQL = DB::table('brasserie')
								->select(DB::raw('countries.country_name, COUNT(*) as total'))	
								->join('countries', 'brasserie.country', '=', 'countries.id_country')
								->whereIn('brasserie.id_brasserie', $myBreweriesIdList)
								->groupBy('brasserie.country')
								->orderByRaw('countries.country_name asc')
								->get();
		$response['myCountries'] = $myBreweriesByCountrySQL;
		
		//List all the breweries  :
		$totalBreweriesSQL = DB::table('brasserie')
						->select(DB::raw('Count(*) as total'))	
						->get();
		$totalBreweries = 0;
		foreach($totalBreweriesSQL as $tmpTotal)
		{
			$totalBreweries = $tmpTotal->total;
			$response['totalBreweries'] = $tmpTotal->total;
		}
		$response['myPercent'] = round(count($knownBreweries) * 100 / $totalBreweries);
		
		return Response::json($response);
	}
}

?>
