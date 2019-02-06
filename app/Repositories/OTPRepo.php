<?php

namespace App\Repositories;

use App\Models\OTPModel as OTPDB;
use Illuminate\Database\QueryException;

class OTPRepo {

	public static function create(array $data){
		try {
			return OTPDB::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// deactive otp 
	public static function deactive($phone_number = null , $user_id = null , $data = null){
		try {
			return OTPDB::where('PhoneNumber',$phone_number)->where('UserId',$user_id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// deactive otp 
	public static function validation($otp_number = null , $phone_number = null , $user_id = null){
		try {
			return OTPDB::where('Status',1)->where('OTPNumber', $otp_number)->where('UserId' , $user_id)->where('PhoneNumber', $phone_number)->get()->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}
}
