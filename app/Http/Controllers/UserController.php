<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use View;

use App\Models\User;
use App\Models\UserBiere;
use App\Models\UserFriend;

class UserController extends BaseController
{
	public function userPresentation($user_id)
	{
		$user = User::find($user_id);
		
		//How many beers tried this user ?
		$beersRated = UserBiere::select(DB::raw('note_biere, COUNT(*) as total'))
							->where('id_user', '=', $user_id)
							->groupBy('note_biere')
							->get();
		
		$beersRate = array(0,0,0,0,0,0);		
		$totalBeersQuotation = 0;
		foreach($beersRated as $quotationBeer)
		{
			$beersRate[$quotationBeer->note_biere] = $quotationBeer->total;
			$totalBeersQuotation += $quotationBeer->total;
		}
		
		//How many breweries tried this user ?
		$totalBreweriesTriedSQL = UserBiere::select(DB::raw('biere.brasserie'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('id_user', '=', $user_id)
							->groupBy('biere.brasserie')
							->get();
							
		$totalBreweriesTried = 0;
							
		foreach($totalBreweriesTriedSQL as $tmpBrewery)
		{
			$totalBreweriesTried++;
		}
		
		//And the best 5 breweries :
		$breweriesTested = UserBiere::select(DB::raw('brasserie.id_brasserie, brasserie.nom_brasserie, AVG(user_biere.note_biere) as averageRate'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->join('brasserie', 'brasserie.id_brasserie', '=', 'biere.brasserie')
							->where('id_user', '=', $user_id)
							->groupBy('biere.brasserie')
							->orderBy('averageRate', 'DESC')
							->take(5)
							->get();
		
		//Get how evolved the rating of this user :
		$ratingPerMonth = UserBiere::select(DB::raw('MONTH(`created_at`) as month, YEAR(created_at) as year, COUNT(*) as total'))
									->where('id_user', '=', $user_id)
									->groupBy('month')
									->groupBy('year')
									->get();		
									
		$tmpRatePerMonthTotal = 0;
		foreach($ratingPerMonth as $tmpRate)
		{
			$tmpRatePerMonthTotal += $tmpRate->total;
			$tmpRate->total = $tmpRatePerMonthTotal;
		}
		
		//Get the type of beer the user prefer (so color, fermentation, malting, type and type2 :
		$bestColor = UserBiere::select(DB::raw('nom_couleur, couleur, AVG(note_biere) as avgNote'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->join('biere_couleur', 'id_couleur', '=' ,'biere.couleur')
								->where('id_user', '=', $user_id) 
								->groupBy('couleur') 
								->orderBy('avgNote', 'DESC')
								->first();
		$bestFermentation = UserBiere::select(DB::raw('nom_fermentation, fermentation, AVG(note_biere) as avgNote'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->join('biere_fermentation', 'id_fermentation', '=' ,'biere.fermentation')
								->where('id_user', '=', $user_id) 
								->groupBy('fermentation') 
								->orderBy('avgNote', 'DESC')
								->first();
		$bestMalting = UserBiere::select(DB::raw('nom_maltage, maltage, AVG(note_biere) as avgNote'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->join('biere_maltage', 'id_maltage', '=' ,'biere.maltage')
								->where('id_user', '=', $user_id) 
								->groupBy('maltage') 
								->orderBy('avgNote', 'DESC')
								->first();
		$bestType = UserBiere::select(DB::raw('nom_type, type, AVG(note_biere) as avgNote'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->join('biere_type', 'id_type', '=' ,'biere.type')
								->where('id_user', '=', $user_id) 
								->groupBy('type') 
								->orderBy('avgNote', 'DESC')
								->first();
		$bestType2 = UserBiere::select(DB::raw('nom_type2, type2, AVG(note_biere) as avgNote'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->join('biere_type2', 'id_type2', '=' ,'biere.type2')
								->where('id_user', '=', $user_id) 
								->groupBy('type2') 
								->orderBy('avgNote', 'DESC')
								->first();
		
		return View::make('users/userView', array(
								'user' => $user, 
								'rates' => $beersRate, 
								'totalBeersRates' => $totalBeersQuotation, 
								'totalBreweriesRates' => $totalBreweriesTried, 
								'breweriesTested' => $breweriesTested, 
								'ratingPerMonth' => $ratingPerMonth,
								'bestColor' => $bestColor,
								'bestFermentation' => $bestFermentation,
								'bestMalting' => $bestMalting,
								'bestType' => $bestType,
								'bestType2' => $bestType2,
								'userFriend' => 0
							));
	}
	
	public function userFriend($friend_id, $friendStatus)
    {
		if(Auth::user() != null && Auth::user()->getAuthIdentifier() != $friend_id && ($friendStatus == 0 || $friendStatus == 1))
		{
			//Are we a friend of this user ?
			$userFriend = UserFriend::where('id_friend', '=', $friend_id)
											->where('id_user', '=', Auth::user()->getAuthIdentifier())
											->first();

			if($userFriend != null && $friendStatus == 0)
			{
				$userFriend->forceDelete();
				return Response::json(0);
			}
			else
			{
				$userFriend = new UserFriend;
				$userFriend->id_user = Auth::user()->getAuthIdentifier();
				$userFriend->id_friend = $friend_id;
				$userFriend->save();
				return Response::json(1);
			}
			
			return Response::json(-1);
		}
		else
			return Response::json("You need to be logged in.");
	}
	
	public function userStats($user_id)
	{
		$user = User::find($user_id);
		
		//How many beers tried this user ?
		$beersRated = UserBiere::select(DB::raw('note_biere, COUNT(*) as total'))
							->where('id_user', '=', $user_id)
							->groupBy('note_biere')
							->get();
		
		$beersRate = array(0,0,0,0,0,0);		
		$totalBeersQuotation = 0;
		$totalBeersRated = 0;
		foreach($beersRated as $quotationBeer)
		{
			$beersRate[$quotationBeer->note_biere] = $quotationBeer->total;
			$totalBeersQuotation += $quotationBeer->total * $quotationBeer->note_biere;
			$totalBeersRated += $quotationBeer->total;
		}
		$averageBeersRate = $totalBeersQuotation/$totalBeersRated;
		
		//How many breweries tried this user ?
		$totalBreweriesTriedSQL = UserBiere::select(DB::raw('biere.brasserie'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('id_user', '=', $user_id)
							->groupBy('biere.brasserie')
							->get();
							
		$totalBreweriesTried = 0;
							
		foreach($totalBreweriesTriedSQL as $tmpBrewery)
		{
			$totalBreweriesTried++;
		}
		
		//How many friends do we have ?
		$totalFriends = UserFriend::select(DB::raw('COUNT(*) as totalFriends'))
							->where('id_user', '=', $user_id)
							->get();
		
		$totalFriendsCleaned = 0;
		foreach($totalFriends as $tmpFriend)
		{
			$totalFriendsCleaned = $tmpFriend->totalFriends;
		}
							
		$totalFollowers = UserFriend::select(DB::raw('COUNT(*) as totalFollowers'))
							->where('id_friend', '=', $user_id)
							->get();
		$totalFollowersCleaned = 0;
		foreach($totalFollowers as $tmpFriend)
		{
			$totalFollowersCleaned = $tmpFriend->totalFollowers;
		}
		
		return Response::json(array("beersRate" => $beersRate, 
									"totalBeersRated" => $totalBeersRated, 
									"averageBeerRate" => round($averageBeersRate*100)/100, 
									"totalBreweriesTried" => $totalBreweriesTried,
									"totalFriends" => $totalFriendsCleaned,
									"totalFollowers" => $totalFollowersCleaned));
	}
}
?>
