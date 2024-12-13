<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_order',
        'payment_method',
        'amount',
        'bukti_tf',
        'status',
        'payment_date',
        'catatan',
        'created_at',
        'updated_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
}
