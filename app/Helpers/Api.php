<?php

namespace App\Helpers;

Class Api{

		public static function format($status, $data, $ErrorMessage){
			$arr['status'] = !empty($status) ? $status : 0;
	        $arr['message'] = !empty($ErrorMessage) ? $ErrorMessage : '';
	        $arr['data']   = !empty($data) ? $data : '';
	        return $arr;	
		} 
}
