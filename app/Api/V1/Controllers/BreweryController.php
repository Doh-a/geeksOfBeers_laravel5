<?php

namespace App\Api\V1\Controllers;

use Auth;
use DB;

use App\Models\Brasserie;

class BreweryController extends BaseController
{

	/**
     * Return all the beers brewed by this brewery
     */
    public function lookForBrewery($searchKey)
    {
        $brasserie = Brasserie::select('id_brasserie', 'nom_brasserie')->whereRaw('LOWER(nom_brasserie) LIKE ?',  array('%' . strtolower($searchKey) . '%'))->get();
		
        return array('foundBreweries' => $brasserie);
    }
}

?>
