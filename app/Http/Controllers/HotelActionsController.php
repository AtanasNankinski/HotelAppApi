<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Validator;

class HotelActionsController extends Controller
{
    function addHotel(Request $req){
    	$rules = array(
    		'hotel_name' => 'required|string|unique:hotels,hotel_name',
    	);
    	$validator = Validator::make($req->all(), $rules);

    	if($validator -> fails()){
    		return response(['message' => "Validation for fields failed!"], 422);
    	}

    	$hotel = Hotel::create([
    		'hotel_name' => $req['hotel_name'],
    	]);

    	return response(['hotel' => $hotel], 201);
    }

    function getHotels(){
    	return Hotel::all();
    }

    function createClient(){

    }
}
