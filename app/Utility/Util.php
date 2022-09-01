<?php

namespace App\Utility;

use Illuminate\Http\Request;
use DateTime;

class Util
{
	static function validateDate($date, $format = 'd-m-Y'){
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    static function compareDates($date1, $date2){
    	$d1 = strtotime($date1);
    	$d2 = strtotime($date2);

    	if ($d1 < $d2) {
    		if ($d1 > time()) {
    			return true;
    		}else {
    			return false;
    		}
    	}else {
    		return false;
    	}
    }
}