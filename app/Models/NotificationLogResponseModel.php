<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLogResponseModel extends Model {

    protected $table = 'notification_log_response';
    protected $primaryKey = 'id';

    protected $fillable = [
        'message'
    ];
    
    public $timestamps = false;

}
