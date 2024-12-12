<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_category',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'created_at',
        'updated_at'
    ];

    public function categories()
    {
        return $this->belongsTo(categories::class,'id_category');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'id_product');
    }
}
