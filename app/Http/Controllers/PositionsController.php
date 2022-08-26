<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\User;
use Validator;

class PositionsController extends Controller
{
    function registerOwner(Request $req){
    	$rules = array(
    		'name' => 'required|string',
    		'email' => 'required|string|unique:users,email',
    		'password' => 'required|string',
            'hotel_name' => 'required|string'
    	);
        $validator = Validator::make($req->all(), $rules);

        if($validator -> fails()){
            return response(['message' => 'Field validation failed!'], 422);
        }

        $hotel = Hotel::where('hotel_name',$req['hotel_name'])->first();

        // Checking if such hotel exists
        if($hotel){
            $user = User::create([
                'name' => $req['name'],
                'email' => $req['email'],
                'password' => bcrypt($req['password']),
                'user_type' => '2',
            ]);

            //$user = User::where('email', $user['email'])->get();
            $hotel->owner = $user['id'];
            $hotel->save();

            return response(['message' => "Owner successfuly added!"], 201);
        }else {
            return response(['message' => 'There is no such hotel'], 404);
        }
    }

    function registerManager(Request $req){
        $rules = array(
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'hotel_name' => 'required|string'
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator -> fails()){
            return response(['message' => 'Field validation failed!'], 422);
        }

        $hotel = Hotel::where('hotel_name',$req['hotel_name'])->first();

        // Checking if such hotel exists
        if($hotel){
            $user = User::create([
                'name' => $req['name'],
                'email' => $req['email'],
                'password' => bcrypt($req['password']),
                'user_type' => '3',
            ]);

            $hotel->manager = $user->id;
            $hotel->save();

            return response(['message' => "Manager successfuly added!"], 201);
        }else {
            return response(['message' => 'There is no such hotel'], 422);
        }
    }

    function registerReceptionist(Request $req){
        $rules = array(
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'manager_id' => 'required|string',
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator -> fails()){
            return response(['message' => 'Field validation failed!'], 422);
        }

        $hotel = Hotel::where('manager',$req['manager_id'])->first();

        if ($hotel) {
            $user = User::create([
                'name' => $req['name'],
                'email' => $req['email'],
                'password' => bcrypt($req['password']),
                'user_type' => '4',
            ]);
            
            $hotel->receptionist = $user->id;
            $hotel->save();

            return response(['message' => "Receptionist successfuly added!", 'hotel' => $hotel], 201);
        }else {
            return response(['message' => 'There is no such hotel'], 422);
        }
    }

    function setHotelOwner(Request $req){
        $rules = array(
            'hotel_name' => 'required|string',
            'owner_email' => 'required|string',
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator -> fails()){
            return response(['message' => 'Field validation failed!'], 422);
        }

        $hotel = Hotel::where('hotel_name', $req['hotel_name'])->first();
        $owner = User::where('email', $req['owner_email'])->first();

        if ($hotel && $owner) {
            if($owner['user_type'] == '2'){
                $hotel->owner = $owner->id;
                $hotel->save();

                $response = ([
                    'hotel' => $hotel,
                    'user' => $owner,
                ]);

                return response($response, 201);
            }else {
                return response(['message' => 'Wrong user type!'], 400);
            }
            $hotel->owner = $owner->id;
            $hotel->save();

            return response(['message' => "Hotel owner successfuly updated!"], 201);
        }else {
            return response(['message' => 'Empty hotel or owner!'], 400);
        }
    }

    function setHotelManager(Request $req){
        $rules = array(
            'hotel_name' => 'required|string',
            'manager_email' => 'required|string',
        );
        $validator = Validator::make($req->all(), $rules);

        if($validator -> fails()){
            return response(['message' => 'Field validation failed!'], 422);
        }

        $hotel = Hotel::where('hotel_name', $req['hotel_name'])->first();
        $manager = User::where('email', $req['manager_email'])->first();

        if ($hotel && $manager) {
            if($manager['user_type'] == '3'){
                $hotel->manager = $manager->id;
                $hotel->save();

                $response = ([
                    'hotel' => $hotel,
                    'user' => $manager,
                ]);

                return response($response, 201);
            }else {
                return response(['message' => 'Wrong user type!'], 400);
            }
            $hotel->manager = $manager->id;
            $hotel->save();

            return response(['message' => "Hotel manager successfuly updated!"], 201);
        }else {
            return response(['message' => 'Empty hotel or owner!'], 400);
        }
    }

    function getAllOwners(){
        return User::where('user_type', '2')->get();
    }

    function getAllManagers(){
        return User::where('user_type', '3')->get();
    }

    function getAllReceptionists(){
        return User::where('user_type', '4')->get();
    }
}
