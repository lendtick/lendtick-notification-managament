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
	public static function deactive($phone_number = null , $data = null){
		try {
			return OTPDB::where('PhoneNumber',$phone_number)->update($data);
			// return OTPDB::where($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	// deactive otp 
	public static function validation($otp_number = null , $phone_number){
		try {
			return OTPDB::where('Status',1)->where('OTPNumber', $otp_number)->where('PhoneNumber', $phone_number)->get()->first();
			// return OTPDB::where($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}
}
