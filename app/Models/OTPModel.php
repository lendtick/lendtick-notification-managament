<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPModel extends Model {

    protected $table = 'notification.OTP';
    protected $primaryKey = 'TransactionOTPId';

    protected $fillable = [
        'TransactionOTPId',
        'OTPNumber',
        'PhoneNumber',
        'CreatedAt',
        'Campaign',
        'Status',
        'UserId'
    ];
    
    public $timestamps = false;

}
