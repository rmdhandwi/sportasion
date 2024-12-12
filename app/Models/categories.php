<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];

    public function products()
    {
        return $this->hasMany(products::class, 'id_category');
    }
}
