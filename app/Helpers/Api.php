<?php

namespace App\Helpers;

Class Api{

		public static function format($status, $data, $ErrorMessage){
			$arr['Status'] = !empty($status) ? $status : '';
	        $arr['Message'] = !empty($ErrorMessage) ? $ErrorMessage : '';
	        $arr['Data']   = !empty($data) ? $data : '';
	        return $arr;	
		} 
}
