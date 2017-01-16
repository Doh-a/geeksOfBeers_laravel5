<?php

namespace App\Http\Controllers;

use DB;
use Response;
use View;

use App\Models\Biere;
use App\Models\Brasserie;
use App\Models\Country;
use App\Models\User;
use App\Models\UserBiere;

class BrasserieController extends BaseController
{

	/**
     * Return all the beers brewed by this brewery
     */
    public function brasseriePresentation($brasserie_id)
    {
        //Find main dta about this brewery
		$brasserie = Brasserie::find($brasserie_id); 
		
		//Find the country :
		$country = Country::find($brasserie->country);
		
		//Find the beers
		$bieres = Biere::where('brasserie', '=', $brasserie_id)
					->get();
		
		$bieresShortView = array();
		foreach ($bieres as $tmpBiere) {
			$bieresShortView[] = View::make('biere/biere_short_view', array('biere' => $tmpBiere));
		}
		
		//Find the fans :
		$fans = User::join('user_brasserie', 'user_brasserie.id_user', '=', 'users.id')
							->where('id_brasserie', '=', $brasserie_id)
							->where('fan', '=', '1')
							->get();
		
		//Find and group the beers per color :
		$colorsRate = Biere::select(DB::raw('couleur, COUNT(*) as count, biere_couleur.nom_couleur'))
							->where('brasserie', '=', $brasserie_id)
							->groupBy('biere.couleur')
							->join('biere_couleur', 'biere_couleur.id_couleur', '=', 'biere.couleur')
							->get();

		//Find and group all rates about this brewery
		$breweryRatesSQL = UserBiere::select(DB::raw('note_biere, COUNT(*) as total'))
							->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->where('brasserie', '=', $brasserie_id)
							->groupBy('note_biere')
							->get();
							
		$breweryRates = array(0,0,0,0,0,0);
		$breweryPercents = array(0,0,0,0,0,0);
		$breweryAverageRate = 0;
		$breweryTotalVotes = 0;
		
		foreach($breweryRatesSQL as $tmpRate)
		{
			$breweryTotalVotes += $tmpRate->total;
			$breweryAverageRate += $tmpRate->total*$tmpRate->note_biere;
			$breweryRates[round($tmpRate->note_biere)] = $tmpRate->total;
		}
		
		if($breweryTotalVotes > 0)
		{
			$breweryAverageRate = $breweryAverageRate/$breweryTotalVotes;
			for($i = 0; $i < 5; $i++)
			{
				$breweryPercents[$i+1] = round($breweryRates[$i+1] * 100 / $breweryTotalVotes,2);
			}
		}
		else
			$breweryAverageRate = 0;
							
		//Find more datas about beers :
		$beersProduced = Biere::where('brasserie', '=', $brasserie_id)
							->where('still_available', '=', '1')
							->count();
		
		return View::make('brasserie/brasserie', array(	'bieres' => $bieresShortView, 
														'brasserie' => $brasserie, 
														'fans' => $fans, 
														'fansTotal' => count($fans), 
														'colorsRate' => $colorsRate, 
														'country' => $country, 
														'beersProduced' => $beersProduced, 
														'beersTotal' => count($bieresShortView), 
														'breweryRates' => $breweryRates, 
														'breweryPercents' => $breweryPercents, 
														'breweryAverageRate' => round($breweryAverageRate,2),
														'breweryTotalVotes' => $breweryTotalVotes
												));
    }

	/**
     * Return comments about brewery
     */
    public function brasserieComments($brasserie_id, $commentsNumber, $from)
    {
		$usersComments = User::select(DB::raw('users.id, users.username, user_brasserie.user_brasserie_id, user_brasserie.commentaire, user_brasserie.fan, user_brasserie.updated_at'))
							->join('user_brasserie', 'user_brasserie.id_user', '=', 'users.id')
							->where('id_brasserie', '=', $brasserie_id)
							->where('commentaire', '!=', '')
							->skip($from)
							->take($commentsNumber)
							->get();
		
		$comments = array();
		
		foreach($usersComments as $tmpUser)
		{
			$comments[]["id_user"] = $tmpUser->id;
			$comments[count($comments)-1]["avatar"] = $tmpUser->getAvatarId();
			$comments[count($comments)-1]["username"] = $tmpUser->username;
			$comments[count($comments)-1]["commentaire"] = $tmpUser->commentaire;
			$comments[count($comments)-1]["commentaire_id"] = $tmpUser->user_brasserie_id;
			$comments[count($comments)-1]["updated_at"] = date("d/m/y H:i", strtotime($tmpUser->updated_at));
			$comments[count($comments)-1]["fan"] = $tmpUser->fan;
		}
		
		return Response::json( $comments);
	}
	
	/**
	 * The home page for the breweries dedicated to logged in users
	 */
	public function brasseriesHomeLogged()
	{
		//List all the breweries already tested by the user :
		$knownBreweries = DB::table('biere')
						->select('brasserie')
						->distinct()
						->join('user_biere', 'user_biere.id_biere', '=', 'biere.id_biere')
						->where('id_user', '=', Auth::user()->getAuthIdentifier())
						->get();
		
		$myBreweriesList = array();
		$myBreweriesIdList = array();
		foreach($knownBreweries as $tmpBrewery)
		{
			$myBreweriesList[] = Brasserie::find($tmpBrewery->brasserie);
			$myBreweriesIdList[] = $tmpBrewery->brasserie;
		}
		if(count($myBreweriesIdList) <= 0)
			$myBreweriesIdList[] = -1;
						
		//Find 15 random breweries that we don't know :
		//SELECT id_brasserie, nom_brasserie FROM brasserie WHERE id_brasserie NOT IN (SELECT DISTINCT(b.brasserie) FROM biere  b, user_biere ub WHERE ub.id_biere = b.id_biere AND ub.id_user = 23) ORDER BY RAND() LIMIT 0, 2
		$breweryUnknownRandom = Brasserie::select(DB::raw('*'))
							->whereNotIn('id_brasserie', $myBreweriesIdList)
							->orderByRaw("RAND()")
							->take(15)
							->get();
		
		//Find 15 breweries enjoyed by the other users that we don't know
		$breweryGradesUnknownSQL = DB::table('brasserie')
							->select(DB::raw('brasserie.nom_brasserie, brasserie.id_brasserie, AVG(user_biere.note_biere) as avg_grade, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->join('user_biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->join('user_friends', 'user_friends.id_friend', '=', 'user_biere.id_user')
							->where('user_friends.id_user', '=', Auth::user()->getAuthIdentifier())
							->whereNotIn('id_brasserie', $myBreweriesIdList)
							->groupBy('brasserie.id_brasserie')
							->orderByRaw('avg_grade desc')
							->take(15)
							->get();
							
		//Find 15 breweries enjoyed by the other users that we could already know
		$breweryGradesSQL = DB::table('brasserie')
							->select(DB::raw('brasserie.nom_brasserie, brasserie.id_brasserie, AVG(user_biere.note_biere) as avg_grade, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->join('user_biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->join('user_friends', 'user_friends.id_friend', '=', 'user_biere.id_user')
							->where('user_friends.id_user', '=', Auth::user()->getAuthIdentifier())
							->groupBy('brasserie.id_brasserie')
							->take(15)
							->get();
							
		//Find the 15th best breweries by the community that we don't know
		$breweryAbsoluteGradesUnknownSQL = DB::table('brasserie')
							->select(DB::raw('brasserie.nom_brasserie, brasserie.id_brasserie, AVG(user_biere.note_biere) as avg_grade, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->join('user_biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->whereNotIn('id_brasserie', $myBreweriesIdList)
							->groupBy('brasserie.id_brasserie')
							->take(15)
							->get();
							
		//Find the 15th best breweries by the community that we could already know
		$breweryAbsoluteGradesSQL = DB::table('brasserie')
							->select(DB::raw('brasserie.nom_brasserie, brasserie.id_brasserie, AVG(user_biere.note_biere) as avg_grade, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->join('user_biere', 'biere.id_biere', '=', 'user_biere.id_biere')
							->groupBy('brasserie.id_brasserie')
							->take(15)
							->get();
	
		return View::make('brasserie/brasseriesHomeLogged', array('myBreweries' => $myBreweriesList,
																'unknwonBreweries' => $breweryUnknownRandom,
																'unknwonBreweriesByGradesFriends' => $breweryGradesUnknownSQL, 
																'breweriesByGradesFriends' => $breweryGradesSQL, 
																'unknwonBreweriesByGrades' => $breweryAbsoluteGradesUnknownSQL, 
																'breweriesByGrades' => $breweryAbsoluteGradesSQL,
		));
	}
	
	/**
	 * THe home page for the breweries (used when users are not logged in
	 */
	public function brasseriesHome()
	{
		//Find 15 random breweries that we don't know :
		//SELECT id_brasserie, nom_brasserie FROM brasserie WHERE id_brasserie NOT IN (SELECT DISTINCT(b.brasserie) FROM biere  b, user_biere ub WHERE ub.id_biere = b.id_biere AND ub.id_user = 23) ORDER BY RAND() LIMIT 0, 2
		$breweryRandom = Brasserie::select(DB::raw('*'))
							->orderByRaw("RAND()")
							->take(16)
							->get();
		
		return View::make('brasserie/brasseriesHome', array('randomBreweries' => $breweryRandom));
	}
	
	public function brasserieSearch($searchKey)
	{
		$brasseries = Brasserie::select(DB::raw('id_brasserie, nom_brasserie, img'))
					->whereRaw('LOWER(nom_brasserie) LIKE ?',  array($searchKey))
					->get();
		
		$idBrasseriesList = array();
		foreach($brasseries as $tmpBrasserie)
		{
			$jsonResult[] = array("id_brasserie" => $tmpBrasserie->id_brasserie, "nom_brasserie" => $tmpBrasserie->nom_brasserie, "img" => $tmpBrasserie->img);
			$idBrasseriesList[] = $tmpBrasserie->id_brasserie;
		}
		
		if(count($idBrasseriesList) > 0)
			$brasseries = Brasserie::select((DB::raw('id_brasserie, nom_brasserie, img')))
					->whereNotIn('id_brasserie', $idBrasseriesList)
					->whereRaw('LOWER(nom_brasserie) LIKE ?',  array($searchKey . '%'))
					->get();
		else
			$brasseries = Brasserie::select((DB::raw('id_brasserie, nom_brasserie, img')))
					->whereRaw('LOWER(nom_brasserie) LIKE ?',  array($searchKey . '%'))
					->get();
		
		foreach($brasseries as $tmpBrasserie)
		{
			$idBrasseriesList[] = $tmpBrasserie->id_brasserie;
			$jsonResult[] = array("id_brasserie" => $tmpBrasserie->id_brasserie, "nom_brasserie" => $tmpBrasserie->nom_brasserie, "img" => $tmpBrasserie->img);
		}
		
		if(count($idBrasseriesList) > 0)
			$brasseries = Brasserie::select((DB::raw('id_brasserie, nom_brasserie, img')))
					->whereNotIn('id_brasserie', $idBrasseriesList)
					->whereRaw('LOWER(nom_brasserie) LIKE ?',  array('%' . $searchKey . '%'))
					->get();
		else
			$brasseries = Brasserie::select((DB::raw('id_brasserie, nom_brasserie, img')))
					->whereRaw('LOWER(nom_brasserie) LIKE ?',  array('%' . $searchKey . '%'))
					->get();
		
		
		foreach($brasseries as $tmpBrasserie)
		{
			$jsonResult[] = array("id_brasserie" => $tmpBrasserie->id_brasserie, "nom_brasserie" => $tmpBrasserie->nom_brasserie, "img" => $tmpBrasserie->img);
			$idBrasseriesList[] = $tmpBrasserie->id_brasserie;
		}
		
		if(count($idBrasseriesList) > 0)
			$brasseriesBieres = DB::table('brasserie')
							->select(DB::raw('biere.nom_biere, brasserie.nom_brasserie, brasserie.id_brasserie, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->whereNotIn('id_brasserie', $idBrasseriesList)
							->whereRaw('LOWER(nom_biere) LIKE ?',  array('%' . $searchKey . '%'))
							->get();
		else
			$brasseriesBieres = DB::table('brasserie')
							->select(DB::raw('biere.nom_biere, brasserie.nom_brasserie, brasserie.id_brasserie, brasserie.img'))
							->join('biere', 'biere.brasserie', '=', 'brasserie.id_brasserie')
							->whereRaw('LOWER(nom_biere) LIKE ?',  array('%' . $searchKey . '%'))
							->get();
						
		foreach($brasseriesBieres as $tmpBrasserie)
		{
			$jsonResult[] = array("id_brasserie" => $tmpBrasserie->id_brasserie, "nom_brasserie" => $tmpBrasserie->nom_brasserie . ' (brasse la ' . $tmpBrasserie->nom_biere .')', "img" => $tmpBrasserie->img);
		}
		
		return Response::json( $jsonResult );
	}
}

?>
