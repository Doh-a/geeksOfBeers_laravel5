<?php

namespace App\Http\Controllers;

use DB;
use Response;

use App\Models\BiereCommentaire;
use App\Models\UserBiere;

class BiereUserController extends BaseController
{

	 /**
     * Ajax method to rate a beer
     */
    public function rateBiere($biere_id, $user_id, $newNote)
    {
	    $biereUser = UserBiere::where("id_user", "=", $user_id)->where("id_biere", "=", $biere_id)->first();
		
		$response = 0;
		$eventRefId = -1;
		
		if($biereUser != null)
		{
			if($newNote == -1)
			{
				$biereUser->delete();
			}
			else
			{
				$biereUser->note_biere = $newNote;
				$reponse = $biereUser->save();
				$eventRefId = $biereUser->user_biere_id;
			}
		}
		else
		{
			if($newNote != -1)
			{
				$newBiereUser = new UserBiere;
				$newBiereUser->id_user = $user_id;
				$newBiereUser->id_biere = $biere_id;
				$newBiereUser->note_biere = $newNote;
				$response = $newBiereUser->save();
				$eventRefId = $newBiereUser->user_biere_id;
			}
		}
		
		//Save this update in the timeline :
		if($eventRefId != -1)
		{
			$newEvent = new Timeline;
			$newEvent->type = EVENT_BEER_GRADE;
			$newEvent->user1 = $user_id;
			$newEvent->ref_id = $eventRefId;
			$newEvent->save();
		}
			
		return Response::json( array('response' => $response) );
    }

	/**
	*	Get all the beers rated by the user (AJAX)
	**/
	public function getBeersRated($user_id)
	{
		$biereUser = UserBiere::select(DB::raw('biere.id_biere, biere.nom_biere, biere.etiquette, note_biere'))
								->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
								->where("id_user", "=", $user_id)
								->orderBy('note_biere', 'DESC')
								->get();
								
		$responseDatas = array();
		
		foreach($biereUser as $tmpBiereUser)
		{
			$responseDatas[] = array("id_biere" => $tmpBiereUser->id_biere, "nom_biere" => $tmpBiereUser->nom_biere, "etiquette" => $tmpBiereUser->etiquette, "note" => $tmpBiereUser->note_biere);
		}
		   
		return Response::json( $responseDatas );
	}
	
	/**
     * Creates or updates a comment about this beer, only for the current user
     */
    public function biereNewComment()
    {
		if(Auth::user() != null && Session::token() === Input::get( '_token' ) && Input::get('beer') != '' ) 
		{
			$userBiereComm = BiereCommentaire::where('id_biere', '=', Input::get('beer'))
											->where('id_user', '=', Auth::user()->getAuthIdentifier())
											->first();
			if($userBiereComm == null)
			{
				$userBiereComm = new BiereCommentaire;
				$userBiereComm->id_user = Auth::user()->getAuthIdentifier();
				$userBiereComm->id_biere = Input::get('beer');
			}
			
			$userBiereComm->commentaire = Input::get('comment');
			$userBiereComm->save();
			$eventRefId = $userBiereComm->id_biere_commentaire;
			
			//Save this update in the timeline :
			if($eventRefId != -1)
			{
				$newEvent = new Timeline;
				$newEvent->type = EVENT_BEER_COMMENT;
				$newEvent->user1 = Auth::user()->getAuthIdentifier();
				$newEvent->ref_id = $eventRefId;
				$newEvent->save();
			}
			
			return Response::json($userBiereComm);
		}
		else
			return Response::json(0);
	}
	
	/**
     * Creates or updates a comment about this beer, only for the current user
     */
    public function getUserBeerComment($beer_id, $user_id)
    {
		if($user_id != null && $beer_id != '' ) 
		{
			$userBiereComm = BiereCommentaire::where('id_biere', '=', $beer_id)
											->where('id_user', '=', $user_id)
											->first();

			if($userBiereComm == null)
				return Response::json(0);
			return Response::json($userBiereComm);
		}
		else
			return Response::json(0);
	}
	
	/**	
	* Return the list of friends who rated this beer, with the grade they gave, plus the average.
	**/
	public function getFriendsRates($beer_id, $user_id)
	{
		$biereUser = UserBiere::select(DB::raw('user_friends.id_friend, users.username, note_biere'))
								->join('user_friends', 'user_friends.id_friend', '=', 'user_biere.id_user')
								->join('users', 'users.id', '=', 'user_friends.id_friend')
								->where("user_friends.id_user", "=", $user_id)
								->where("user_biere.id_biere", "=", $beer_id)
								->orderBy('note_biere', 'DESC')
								->get();
						
		$responseDatas = array();
		$un = 0;
		$deux = 0;
		$trois = 0;
		$quatre = 0;
		$cinq = 0;
		$total = 0;
		$totalRates = 0;
		foreach($biereUser as $tmpBiereUser)
		{
			if($tmpBiereUser->note_biere > 0 && $tmpBiereUser->note_biere <= 1)
				$un++;
			if($tmpBiereUser->note_biere > 1 && $tmpBiereUser->note_biere <= 2)
				$deux++;
			if($tmpBiereUser->note_biere > 2 && $tmpBiereUser->note_biere <= 3)
				$trois++;
			if($tmpBiereUser->note_biere > 3 && $tmpBiereUser->note_biere <= 4)
				$quatre++;
			if($tmpBiereUser->note_biere > 4)
				$cinq++;

			$total++;
			$totalRates += $tmpBiereUser->note_biere;
			
			$tmpUser = User::find($tmpBiereUser->id_friend);
			
			$responseDatas[] = array( "note" => $tmpBiereUser->note_biere, "username" => $tmpBiereUser->username, "id_user" => $tmpBiereUser->id_friend, "avatar" => $tmpUser->getAvatarId());
		}
		
		$responseDatas['total'] = $total;
		$responseDatas['average'] = ($total > 0) ? round($totalRates/$total,1) : 0;
		$responseDatas['un'] = $un;
		$responseDatas['deux'] = $deux;
		$responseDatas['trois'] = $trois;
		$responseDatas['quatre'] = $quatre;
		$responseDatas['cinq'] = $cinq;
		
		return Response::json( $responseDatas );
	}
}

?>
