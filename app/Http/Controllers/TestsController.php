<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestsController extends Controller
{
    function testApi(){
    	return response(['message' => "Connection with api succesfull!"], 200);
    }
}
