<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Response;

use App\Models\Biere;
use App\Models\Brasserie;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showIndex()
	{
		
		if(Auth::user() != null)
			return view('homeLogged');
		else
			return view('home');
	}
	
	public function randomBeers()
	{
		$selectBeers = DB::table('biere')
                     ->select(DB::raw('nom_biere, biere.id_biere, etiquette, (SELECT AVG(note_biere) FROM user_biere WHERE user_biere.id_biere = biere.id_biere) as avg_grade, (SELECT COUNT(note_biere) FROM user_biere WHERE user_biere.id_biere = biere.id_biere) as rate_count'))
					 ->whereRaw('(SELECT COUNT(note_biere) FROM user_biere WHERE user_biere.id_biere = biere.id_biere) > 2')
					 ->orderByRaw("RAND()")
					 ->take(3)
                     ->get();
		
		return Response::json( $selectBeers );
	}
	
	public function homeStats()
	{
		// Stats about the grades
		$selectGrades = DB::table('user_biere')
                     ->select(DB::raw('note_biere, count(*) as rate_count'))
                     ->groupBy('note_biere')
                     ->get();
		
		$biereFinalRates = array(0,0,0,0,0,0);
		$biereFinalPercents = array(0,0,0,0,0,0);
		$totalVotes = 0;
		
		foreach($selectGrades as $tmpRate)
		{
			$biereFinalRates[$tmpRate->note_biere] += $tmpRate->rate_count;
			$totalVotes += $tmpRate->rate_count;
		}
		
		// Stats about the beers :
		$totalBeers = Biere::count();
		
		// How fast did it grow ?
		$beforeBeersStats = DB::table('biere')
							->select(DB::raw('COUNT(*) as total'))
							->whereRaw('created_at < DATE(NOW()) - INTERVAL 6 MONTH')
							->first();
							
		$beersStatsPerMonth = DB::table('biere')
							->select(DB::raw('DATE_FORMAT(created_at, "%m %Y") date, COUNT(*) as total'))
							->whereRaw('created_at >= DATE(NOW()) - INTERVAL 6 MONTH GROUP BY YEAR(created_at), MONTH(created_at)')
							->orderBy('created_at')
							->get();
		
		$tmpTotalBeers = $beforeBeersStats->total;
		$tmpTotalBeersPerMonth	= array();
		$totalBeersPerMonth	= array();
		
		foreach($beersStatsPerMonth as $tmpBeerPerMonth)
		{
			$tmpTotalBeersPerMonth[$tmpBeerPerMonth->date] = $tmpBeerPerMonth->total;
		}
		
		for($tmpMonth = 6; $tmpMonth >= 0; $tmpMonth--)
		{
			$tmpDate = strtotime(date('Y-m-d') . ' - ' . $tmpMonth . ' months');
			if(!isset($tmpTotalBeersPerMonth[date('m Y', $tmpDate)]))
				$totalBeersPerMonth[date('m Y', $tmpDate)] = $tmpTotalBeers;
			else
			{
				$tmpTotalBeers += $tmpTotalBeersPerMonth[date('m Y', $tmpDate)];
				$totalBeersPerMonth[date('m Y', $tmpDate)] = $tmpTotalBeers;
			}
		}
		
		// Stats about the breweries :
		$totalBreweries = Brasserie::count();
		
		// How fast did it grow ?
		$beforeBreweriesStats = DB::table('brasserie')
							->select(DB::raw('COUNT(*) as total'))
							->whereRaw('created_at < DATE(NOW()) - INTERVAL 6 MONTH')
							->first();
							
		$breweriesStatsPerMonth = DB::table('brasserie')
							->select(DB::raw('DATE_FORMAT(created_at, "%m %Y") date, COUNT(*) as total'))
							->whereRaw('created_at >= DATE(NOW()) - INTERVAL 6 MONTH GROUP BY YEAR(created_at), MONTH(created_at)')
							->orderBy('created_at')
							->get();
		
		$tmpTotalBreweries = $beforeBreweriesStats->total;
		$tmpTotalBreweriesPerMonth	= array();
		$totalBreweriesPerMonth	= array();
		
		foreach($breweriesStatsPerMonth as $tmpBreweriesPerMonth)
		{
			$tmpTotalBreweriesPerMonth[$tmpBreweriesPerMonth->date] = $tmpBreweriesPerMonth->total;
		}
		
		for($tmpMonth = 6; $tmpMonth >= 0; $tmpMonth--)
		{
			$tmpDate = strtotime(date('Y-m-d') . ' - ' . $tmpMonth . ' months');
			if(!isset($tmpTotalBreweriesPerMonth[date('m Y', $tmpDate)]))
				$totalBreweriesPerMonth[date('m Y', $tmpDate)] = $tmpTotalBreweries;
			else
			{
				$tmpTotalBreweries += $tmpTotalBreweriesPerMonth[date('m Y', $tmpDate)];
				$totalBreweriesPerMonth[date('m Y', $tmpDate)] = $tmpTotalBreweries;
			}
		}
		
		// Stats about the countries :
		$totalCountriesSQL = Brasserie::select(DB::raw('country, COUNT(country) as breweriesInCountry, countries.country_name, countries.id_country'))
							->join('countries', 'countries.id_country', '=', 'brasserie.country')
							->groupBy('country')
							->orderByRaw('COUNT(country) DESC')
							->get();
		
		$countriesList = array();
		$totalCountries = 0;
		
		foreach($totalCountriesSQL as $tmpCountry)
		{
			$countriesList[] = array($tmpCountry->country_name, $tmpCountry->breweriesInCountry);
			$totalCountries ++;
		}
		
		// Get the favorite beer on Geeks of Beers :
		$bestBeer = DB::table('user_biere')
					->select(DB::raw('AVG(note_biere) avg_grade, user_biere.id_biere, biere.nom_biere, biere.etiquette'))
					->join('biere', 'biere.id_biere', '=', 'user_biere.id_biere')
					->groupBy('user_biere.id_biere')
					->orderByRaw('AVG(note_biere) DESC')
					->first();
					
		// Get the favorite brewery on Geekf of Beers :
		$bestBrewery = DB::table('user_brasserie')
						->select(DB::raw('COUNT(*) fans_count, brasserie.id_brasserie, brasserie.nom_brasserie, brasserie.img'))
						->join('brasserie', 'brasserie.id_brasserie',  '=', 'user_brasserie.id_brasserie')
						->groupBy('user_brasserie.id_brasserie')
						->orderByRaw('COUNT(*) DESC')
						->first();
		
		// Send the response as an xml : 
		return Response::json( array('totalVotes' => $totalVotes, 
									'gradesRepartition' => $biereFinalRates, 
									'totalBreweries' => $totalBreweries, 
									'breweriesPerMonth' => $totalBreweriesPerMonth,
									'totalBeers' => $totalBeers,
									'beersPerMonth' => $totalBeersPerMonth,
									'countriesList' => $countriesList,
									'totalCountries' => $totalCountries,
									'bestBeer' => $bestBeer,
									'bestBrewery' => $bestBrewery
							));
	}
}
