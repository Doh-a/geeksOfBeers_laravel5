<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Auth;
use DB;
use Response;

use App\Models\BiereCommentaire;
use App\Models\EventsComment;
use App\Models\EventsLike;
use App\Models\Timeline;
use App\Models\User;
use App\Models\UserBiere;
use App\Models\UserBrasserie;
use App\Models\UserFriend;

class TimelineController extends Controller
{
	public function getMainTimeline($user_id)
	{
		if($user_id == -1)
			return $this->getAllTimeline();
		
		//List all my friends :
		$myFriendsSQL = UserFriend::select('id_friend')
						->where('id_user', '=', $user_id)
						->get();
						
		$myFriendId = array();
		foreach($myFriendsSQL as $tmpFriend)
		{
			$myFriendId[] = $tmpFriend->id_friend;
		}
		
		$lastEventsSQL = Timeline::select(DB::raw('id_event, type, user1, user2, ref_id, updated_at'))
						->where('user1', '=', $user_id)
						->orWhere('user2', '=', $user_id)
						->orWhereIn('user1', $myFriendId)
						->orWhereIn('user2', $myFriendId)
						->orderBy('updated_at', 'desc')
						->limit(50)
						->get();
					
		$jsonResponse = array();
					
		foreach($lastEventsSQL as $tmpEvent)
		{
			if($tmpEvent->type != 0)
			{
				$tmpResponse = new EventMessage;
				$tmpResponse->ref_id = -1;
				
				$likeSQL = DB::table('events_like')
							->select(DB::raw('COUNT(*) as totalLikes'))
							->where('id_event', '=', $tmpEvent->id_event)
							->where('like_type', '=', 1)
							->get();
				if($likeSQL != null)		
					$tmpResponse->likesCount = $likeSQL[0]->totalLikes;
			
				$dislikeSQL = DB::table('events_like')
							->select(DB::raw('COUNT(*) as totalDislikes'))
							->where('id_event', '=', $tmpEvent->id_event)
							->where('like_type', '=', 0)
							->get();
				
				if($dislikeSQL != null)
				{
					$tmpResponse->dislikesCount = $dislikeSQL[0]->totalDislikes;
				}
				
				$myLikeSQL = EventsLike::where('id_user', '=', Auth::user()->getAuthIdentifier())
									->where('id_event', '=', $tmpEvent->id_event)
									->first();
				$myLike = -1;
				if($myLikeSQL != null)
					$myLike = $myLikeSQL->like_type;
				
				$commentsEventSQL = EventsComment::select(DB::raw('events_comment.id_user, users.username, events_comment.comment, events_comment.id_comment, events_comment.created_at'))
											->join('users', 'users.id', '=', 'events_comment.id_user')
											->where('events_comment.id_event', '=', $tmpEvent->id_event)
											->orderBy('events_comment.created_at', 'desc')
											->take(5)
											->get();
				
				$commentsList = array();
				foreach($commentsEventSQL as $tmpComment)
				{
					$tmpUser = User::find($tmpComment->id_user);
					$commentsList[] = array("id_event" => $tmpComment->id_event, 
											"id_user" => $tmpComment->id_user, 
											"username" => $tmpComment->username, 
											"avatar_id" => $tmpUser->getAvatarId(),
											"id_comment" => $tmpComment->id_comment, 
											"comment" => $tmpComment->comment,
											"created_at" => $tmpComment->created_at);
				}
				
				
				switch($tmpEvent->type)
				{
					case EVENT_BEER_GRADE :
						$beerGradeSQL = UserBiere::select(DB::raw('user_biere.user_biere_id, user_biere.id_user, users.username, biere.nom_biere, biere.id_biere, user_biere.note_biere'))
										->where("user_biere.user_biere_id", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'user_biere.id_user')
										->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
										->first();
						
						if($beerGradeSQL != null)
						{
							$tmpUser = User::find($beerGradeSQL->id_user);
							
							$tmpResponse->mainMessage = 'a noté la bière <a href="biere/' . $beerGradeSQL->id_biere . '">' . $beerGradeSQL->nom_biere . '</a> : ' . $beerGradeSQL->note_biere . ' / 5'; 
							
							$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
							
							$tmpResponse->username = $tmpUser->username;
							
							$tmpResponse->userid = $beerGradeSQL->id_user;
							
							$tmpResponse->eventDate = $tmpEvent->updated_at;
							
							$tmpResponse->ref_id = $tmpEvent->ref_id;
							
							$tmpResponse->eventId = $tmpEvent->id_event;
							
							$tmpResponse->myLike = $myLike;
							
							$tmpResponse->comments = $commentsList;
						}
						break;
					case EVENT_BEER_COMMENT :
						$beerCommentsSQL = BiereCommentaire::select(DB::raw('biere_commentaires.id_biere_commentaire, biere_commentaires.id_user, biere_commentaires.commentaire, users.username, biere.nom_biere, biere.id_biere'))
										->where("biere_commentaires.id_biere_commentaire", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'biere_commentaires.id_user')
										->join('biere', 'biere.id_biere', '=', 'biere_commentaires.id_biere')
										->first();
						
						if($beerCommentsSQL != null)
						{
							$tmpUser = User::find($beerCommentsSQL->id_user);
							
							$tmpResponse->mainMessage = 'a commenté la bière <a href="biere/' . $beerCommentsSQL->id_biere . '">' . $beerCommentsSQL->nom_biere . '</a> : <p style="margin-left : 50px; margin-bottom : 25px; border : 1px solid #DDD; padding : 5px;">' . $beerCommentsSQL->commentaire . '</p>'; 
							
							$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
							
							$tmpResponse->username = $tmpUser->username;
							
							$tmpResponse->userid = $beerCommentsSQL->id_user;
							
							$tmpResponse->eventDate = $tmpEvent->updated_at;
							
							$tmpResponse->ref_id = $tmpEvent->ref_id;
							
							$tmpResponse->eventId = $tmpEvent->id_event;
							
							$tmpResponse->myLike = $myLike;
							
							$tmpResponse->comments = $commentsList;
						}
						break;
					case EVENT_BREWERY_FAN :
						$breweryFanSQL = UserBrasserie::select(DB::raw('user_brasserie.id_brasserie, user_brasserie.id_user, brasserie.nom_brasserie'))
										->where("user_brasserie.user_brasserie_id", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'user_brasserie.id_user')
										->join('brasserie', 'brasserie.id_brasserie', '=', 'user_brasserie.id_brasserie')
										->first();
						
						$tmpUser = User::find($breweryFanSQL->id_user);
						
						$tmpResponse->mainMessage = 'est maintenant fan de <a href="brasserie/' . $breweryFanSQL->id_brasserie . '">' . $breweryFanSQL->nom_brasserie . '</a>'; 
						
						$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
						
						$tmpResponse->username = $tmpUser->username;
						
						$tmpResponse->userid = $breweryFanSQL->id_user;
						
						$tmpResponse->eventDate = $tmpEvent->updated_at;
						
						$tmpResponse->ref_id = $tmpEvent->ref_id;
						
						$tmpResponse->eventId = $tmpEvent->id_event;
						
						$tmpResponse->myLike = $myLike;
						
						$tmpResponse->comments = $commentsList;
						
						break;
				}
				
				if($tmpResponse->ref_id != -1)
					$jsonResponse[] = $tmpResponse;
			}
		}
		
		return Response::json( $jsonResponse );
	}
	
	public function getAllTimeline()
	{
		$lastEventsSQL = Timeline::select(DB::raw('id_event, type, user1, user2, ref_id, updated_at'))
						->orderBy('updated_at', 'desc')
						->limit(50)
						->get();
					
		$jsonResponse = array();
					
		foreach($lastEventsSQL as $tmpEvent)
		{
			if($tmpEvent->type != 0)
			{
				$tmpResponse = new EventMessage;
				$tmpResponse->ref_id = -1;
				
				$likeSQL = DB::table('events_like')
							->select(DB::raw('COUNT(*) as totalLikes'))
							->where('id_event', '=', $tmpEvent->id_event)
							->where('like_type', '=', 1)
							->get();
				if($likeSQL != null)		
					$tmpResponse->likesCount = $likeSQL[0]->totalLikes;
			
				$dislikeSQL = DB::table('events_like')
							->select(DB::raw('COUNT(*) as totalDislikes'))
							->where('id_event', '=', $tmpEvent->id_event)
							->where('like_type', '=', 0)
							->get();
				
				if($dislikeSQL != null)
				{
					$tmpResponse->dislikesCount = $dislikeSQL[0]->totalDislikes;
				}
				
				$commentsEventSQL = EventsComment::select(DB::raw('events_comment.id_user, users.username, events_comment.comment, events_comment.id_comment, events_comment.created_at'))
											->join('users', 'users.id', '=', 'events_comment.id_user')
											->where('events_comment.id_event', '=', $tmpEvent->id_event)
											->orderBy('events_comment.created_at', 'desc')
											->take(5)
											->get();
				
				$commentsList = array();
				foreach($commentsEventSQL as $tmpComment)
				{
					$tmpUser = User::find($tmpComment->id_user);
					$commentsList[] = array("id_event" => $tmpComment->id_event, 
											"id_user" => $tmpComment->id_user, 
											"username" => $tmpComment->username, 
											"avatar_id" => $tmpUser->getAvatarId(),
											"id_comment" => $tmpComment->id_comment, 
											"comment" => $tmpComment->comment,
											"created_at" => $tmpComment->created_at);
				}
				
				
				switch($tmpEvent->type)
				{
					case EVENT_BEER_GRADE :
						$beerGradeSQL = UserBiere::select(DB::raw('user_biere.user_biere_id, user_biere.id_user, users.username, biere.nom_biere, biere.id_biere, user_biere.note_biere'))
										->where("user_biere.user_biere_id", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'user_biere.id_user')
										->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
										->first();
						
						if($beerGradeSQL != null)
						{
							$tmpUser = User::find($beerGradeSQL->id_user);
							
							$tmpResponse->mainMessage = 'a noté la bière <a href="biere/' . $beerGradeSQL->id_biere . '">' . $beerGradeSQL->nom_biere . '</a> : ' . $beerGradeSQL->note_biere . ' / 5'; 
							
							$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
							
							$tmpResponse->username = $tmpUser->username;
							
							$tmpResponse->userid = $beerGradeSQL->id_user;
							
							$tmpResponse->eventDate = $tmpEvent->updated_at;
							
							$tmpResponse->ref_id = $tmpEvent->ref_id;
							
							$tmpResponse->eventId = $tmpEvent->id_event;
							
							$tmpResponse->comments = $commentsList;
						}
						break;
					case EVENT_BEER_COMMENT :
						$beerCommentsSQL = BiereCommentaire::select(DB::raw('biere_commentaires.id_biere_commentaire, biere_commentaires.id_user, biere_commentaires.commentaire, users.username, biere.nom_biere, biere.id_biere'))
										->where("biere_commentaires.id_biere_commentaire", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'biere_commentaires.id_user')
										->join('biere', 'biere.id_biere', '=', 'biere_commentaires.id_biere')
										->first();
						
						if($beerCommentsSQL != null)
						{
							$tmpUser = User::find($beerCommentsSQL->id_user);
							
							$tmpResponse->mainMessage = 'a commenté la bière <a href="biere/' . $beerCommentsSQL->id_biere . '">' . $beerCommentsSQL->nom_biere . '</a> : <p style="margin-left : 5px; margin-bottom : 25px; border : 1px solid #DDD; padding : 5px;">' . $beerCommentsSQL->commentaire . '</p>'; 
							
							$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
							
							$tmpResponse->username = $tmpUser->username;
							
							$tmpResponse->userid = $beerCommentsSQL->id_user;
							
							$tmpResponse->eventDate = $tmpEvent->updated_at;
							
							$tmpResponse->ref_id = $tmpEvent->ref_id;
							
							$tmpResponse->eventId = $tmpEvent->id_event;
							
							$tmpResponse->comments = $commentsList;
						}
						break;
					case EVENT_BREWERY_FAN :
						$breweryFanSQL = UserBrasserie::select(DB::raw('user_brasserie.id_brasserie, user_brasserie.id_user, brasserie.nom_brasserie'))
										->where("user_brasserie.user_brasserie_id", "=", $tmpEvent->ref_id)
										->join('users', 'users.id', '=', 'user_brasserie.id_user')
										->join('brasserie', 'brasserie.id_brasserie', '=', 'user_brasserie.id_brasserie')
										->first();
						
						$tmpUser = User::find($breweryFanSQL->id_user);
						
						$tmpResponse->mainMessage = 'est maintenant fan de <a href="brasserie/' . $breweryFanSQL->id_brasserie . '">' . $breweryFanSQL->nom_brasserie . '</a>'; 
						
						$tmpResponse->img = "assets/img/avatars/" . $tmpUser->getAvatarId() . "_40.jpg";
						
						$tmpResponse->username = $tmpUser->username;
						
						$tmpResponse->userid = $breweryFanSQL->id_user;
						
						$tmpResponse->eventDate = $tmpEvent->updated_at;
						
						$tmpResponse->ref_id = $tmpEvent->ref_id;
						
						$tmpResponse->eventId = $tmpEvent->id_event;
						
						$tmpResponse->comments = $commentsList;
						
						break;
				}
				
				if($tmpResponse->ref_id != -1)
					$jsonResponse[] = $tmpResponse;
			}
		}
		
		return Response::json( $jsonResponse );
	}
}

class EventMessage
{
	public $eventId = -1;
	
	public $type = 0;
	
	public $mainMessage = "";
	
	public $img = "";
	
	public $username = "";
	
	public $userid = "";
	
	public $eventDate = "";
	
	public $ref_id;
	
	public $likesCount = 0;
	
	public $dislikesCount = 0;
	
	public $myLike = -1;
	
	public $comments = null;
}

?>