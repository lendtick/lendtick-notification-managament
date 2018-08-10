<?php

namespace App\Repositories;

use App\Models\NotificationModel as NotificationDB;
use Illuminate\Database\QueryException;

class NotificationRepo{
	
	public function all($columns = array('*')){
		try {
			if($columns == array('*')) return NotificationDB::all();
			else return NotificationDB::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function create(array $data){
		try {
			return NotificationDB::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function find($column, $value){
		try {
			return NotificationDB::where($column, $value)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function update($id, array $data){
		try { 
			return NotificationDB::where('MsProductId',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public function delete($id){
		try { 
			return NotificationDB::where('MsProductId',$id)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
		
	}

	public function last(){
		try{
			return NotificationDB::orderBy('Id', 'desc')->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}
}
