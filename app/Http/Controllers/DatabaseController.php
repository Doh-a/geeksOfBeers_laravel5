<?php

namespace App\Http\Controllers;

use Auth;
use Input;
use View;

use App\Models\Biere;
use App\Models\Brasserie;
use App\Models\Country;

class DatabaseController extends BaseController
{

	public function breweries()
    {
        if(!Auth::check() || Auth::user()->userRole()->role_id < 2)
            return redirect()->action('HomeController@showIndex');
            
        $breweries = Brasserie::all();
        $breweriesCount = Brasserie::all()->count();
        $notApprovedBreweries = Brasserie::where('approved', '=', 0)->count();
        $rateNotApproved = round($notApprovedBreweries * 100 / $breweriesCount, 2);

        return View::make('databaseTools/brasseries', array(
            'breweries' => $breweries,
            'breweriesCount' => $breweriesCount,
            'notApprovedBreweries' => $notApprovedBreweries,
            'rateNotApproved' => $rateNotApproved
        ));
    }

    public function editBreweryForm($id_brewery)
    {
        if(!Auth::check() || Auth::user()->userRole()->role_id < 2)
            return redirect()->action('HomeController@showIndex');

        $brewery = Brasserie::find($id_brewery);
        $countries = Country::orderBy('country_name', 'asc')->lists('country_name', 'id_country');
        
        return View::make('databaseTools/brasserieEdit')->with(array('brewery' => $brewery, 'countriesList' => $countries));
    }

    public function editBrewery()
    {
        if(!Auth::check() || Auth::user()->userRole()->role_id < 2)
            return redirect()->action('HomeController@showIndex');
            
        $brewery = Brasserie::find(Input::get("breweryId"));

        if($brewery != null)
        {
            $brewery->nom_brasserie = Input::get("brewery_name");
            $brewery->founded = Input::get("brewery_founded");
            $brewery->description_brasserie = Input::get("brewery_description");
            $brewery->country = Input::get("country");
            $brewery->lat_brewery = Input::get("brewery_lat");
            $brewery->long_brewery = Input::get("brewery_long");
            $brewery->approved = Input::get("brewery_approved");
            $brewery->save();
        }

        return redirect()->action('DatabaseController@breweries');
    }

    public function beers()
    {
        if(!Auth::check() || Auth::user()->userRole()->role_id < 2)
            return redirect()->action('HomeController@showIndex');

        $beers = Biere::all();
        $beersCount = Biere::all()->count();
        $notApprovedBeers = Biere::where('approved', '=', 0)->count();
        $rateNotApproved = round($notApprovedBeers * 100 / $beersCount, 2);

        return View::make('databaseTools/beers', array(
            'beers' => $beers,
            'beersCount' => $beersCount,
            'notApprovedBeers' => $notApprovedBeers,
            'rateNotApproved' => $rateNotApproved
        ));
    }
}

?>
