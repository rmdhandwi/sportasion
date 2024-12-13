<?php

namespace App\Models;
use App\Models\products as ModelsProducts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    //
    use HasFactory;
    public $timestamps = false;
    protected $table = 'order_details';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_order',
        'id_product',
        'quantity',
        'price',
        'created_at',
        'updated_at'
    ];

    public function product()
    {
        return $this->belongsTo(ModelsProducts::class,'id_product','id');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
