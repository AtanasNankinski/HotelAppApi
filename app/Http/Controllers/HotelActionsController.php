<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Client;
use App\Models\User;
use App\Models\Reservation;
use App\Utility\Util;
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

    function getHotelsByOwner($owner_id){
        if ($owner_id != null && $owner_id != "") {
            return Hotel::where('owner', $owner_id)->get();
        }else {
            return response(['message' => 'Owner id is empty!'], 422);
        }
    }

    function getHotelsByManager($manager_id){
        if ($manager_id != null && $manager_id != "") {
            return Hotel::where('manager', $manager_id)->first();
        }else {
            return response(['message' => 'Owner id is empty!'], 422);
        }
    }

    function getHotelByReceptionist($receptionist_id){
        if ($receptionist_id != null && $receptionist_id != "") {
            return Hotel::where('receptionist', $receptionist_id)->first();
        }else {
            return response(['message' => 'Receptionist id is empty!'], 422);
        }
    }

    function createClient(Request $req){
        $rules = array(
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:clients,email',
            'receptionist_id' => 'required|integer'
        );
        $validator = Validator::make($req->all(), $rules);

        if ($validator -> fails()) {
            return response(['message' => "Validation for fields failed!"], 422);
        }

        $receptionist = User::where('id', $req['receptionist_id'])->first();

        if(!$receptionist || $receptionist->user_type != 4){
            return response(['message' => 'Error with receptionist account!'], 422);
        }

        $hotel = Hotel::where('receptionist', $receptionist->id)->first();
        if (!$hotel) {
            return response(['message' => 'Error with getting hotel data!'], 422);
        }

        $client = Client::create([
            'first_name' => $req['first_name'],
            'last_name' => $req['last_name'],
            'email' => $req['email'],
            'hotel' => $hotel->id
        ]);

        return response(['message' => 'Client created successfully'], 201);
    }

    function createReservation(Request $req){
        $rules = array(
            'reservation_type' => 'required|string',
            'room_type' => 'required|string',
            'room_number' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'additional_service' => 'required|boolean',
            'client_email' => 'required|string',
            'hotel_id' => 'required|string'
        );
        $validator = Validator::make($req->all(), $rules);

        if ($validator -> fails()) {
            $failedRules = $validator->failed();
            return response(['message' => $failedRules], 422);
        }

        if (Util::validateDate($req['start_date']) && Util::validateDate($req['end_date'])) {
            if (Util::compareDates($req['start_date'], $req['end_date'])) {
                $client = Client::where('email', $req['client_email'])->first();
                if ($client && $client['reservation'] == null) {
                    $reservation = Reservation::create([
                        'reservation_type' => $req['reservation_type'],
                        'room_type' => $req['room_type'],
                        'room_number' => $req['room_number'],
                        'start_date' => $req['start_date'],
                        'end_date' => $req['end_date'],
                        'additional_service' => $req['additional_service'],
                        'hotel' => $req['hotel_id']
                    ]);

                    $client->reservation = $reservation->id;
                    $client->save();

                    return response(['message' => 'Reservation created successfully!'], 201);
                }
                return response(['message' => 'No such client or the client already has reservation!'], 422);
            }
            return response(['message' => 'The Dates range is invalid'], 422);
        }
        return response(['message' => 'Datetime validation failed!'], 422);
    }

    function getClients(){
        return Client::all();
    }

    function getReservations(){
        return Reservation::all();
    }
}
