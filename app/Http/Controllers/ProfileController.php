<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function store(Request $r){
        sleep(3);
        return json_encode(['message'=>'ok','return'=>$r->all()]);
    }
}
