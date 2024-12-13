<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_user',
        'id_product',
        'quantity',
        'order_date',
        'status',
        'total_price',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function product()
    {
        return $this->belongsTo(products::class, 'id_product', 'id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaction::class, 'id_order', 'id');
    }
}
