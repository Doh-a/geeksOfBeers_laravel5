<?php

namespace App\Api\V1\Controllers;

use Auth;
use DB;

use App\Models\Avatar;
use App\Models\User;

class UserController extends BaseController
{

	public function loggedUserInfo()
    {
        $user = User::find(Auth::user()->getAuthIdentifier());
        
        return array("user" => $user, "avatar" => Auth::user()->getAvatarId());
    }
}

?>
