<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model {

    protected $table = 'notification.notification_log';
    protected $primaryKey = 'notification_log_id';

    protected $fillable = [
        'notification_log_id',
        'type',
        'subject',
        'body',
        'to',
        'attachment',
        'send_date'
    ];
    
    public $timestamps = false;

}
