<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Input;
use Response;
use Session;

use App\Models\EventsComment;

class UserEvents extends BaseController
{
	public function likeEvent($id_eventString, $likeType)
	{
		$id_event = intval($id_eventString);
		if(Auth::user() != null && is_int($id_event) && $id_event > 0  && ($likeType == 0 || $likeType == 1))
		{
			//Test if there is already a like / dislike from this user about this event :
			$testLikeSQL = EventsLike::where('id_event', '=', $id_event)
									->where('id_user', '=', Auth::user()->getAuthIdentifier())
									->first();
									
			$userLike = $likeType;
			
			if($testLikeSQL != null)
			{
				if($testLikeSQL->like_type != $likeType)
				{
					$newLike = new EventsLike;
					$newLike->id_event = $id_event;
					$newLike->id_user = Auth::user()->getAuthIdentifier();
					$newLike->like_type = $likeType;
					$newLike->save();
				}
				else
					$userLike = -1;
				
				$testLikeSQL->delete();
			}
			else
			{
				$newLike = new EventsLike;
				$newLike->id_event = $id_event;
				$newLike->id_user = Auth::user()->getAuthIdentifier();
				$newLike->like_type = $likeType;
				$newLike->save();
			}
			
			$totalLikesSQL = EventsLike::select(DB::raw('like_type, COUNT(*) as total'))
									->where('id_event', '=', $id_event)
									->groupBy('like_type')
									->get();
			
			//Init the retur value
			$response[0] = 0;
			$response[1] = 0;
			
			//Fill it with the actual values
			foreach($totalLikesSQL as $tmpTotalLike)
			{
				$response[$tmpTotalLike->like_type] = $tmpTotalLike->total;
			}
			
			//Add the user personnal selection
			$response['userLike'] = $userLike;
									
			return Response::json($response);
		}
		
		return Response::json(-1);
	}
	
	public function commentEvent($id_eventString)
	{
		$id_event = intval($id_eventString);
		
		if(Auth::user() != null && Session::token() === Input::get( '_token' ) && is_int($id_event) && $id_event > 0  && Input::get('comment') != '')
		{
			$newComment = new EventsComment;
			$newComment->id_event = $id_event;
			$newComment->id_user = Auth::user()->getAuthIdentifier();
			$newComment->comment = Input::get('comment');
			$newComment->save();
			return Response::json($newComment);
		}
		
		return Response::json(-1);
	}
}

?>