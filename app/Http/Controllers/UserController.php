<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user , Request $request){
        return apiResponse(true, 'data retrieved successfully', 200, $user);

    }
}
