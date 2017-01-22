<?php

namespace App\Api\V1\Controllers;

use Auth;
use DB;

use App\Models\TypeAmericain;
use App\Models\TypeBelge;

class TypeController extends BaseController
{

	/**
     * Return all the beers brewed by this brewery
     */
    public function listAll()
    {
        $typeAmericain = TypeAmericain::orderBy('nom_type', 'asc')->get();
        $typeBelge = TypeBelge::orderBy('nom_type2', 'asc')->get();

        return array(
                    'type' => $typeAmericain,
                    'type2' => $typeBelge,
                    );
    }
}

?>
