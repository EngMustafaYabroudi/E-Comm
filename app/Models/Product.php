<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'deleted_at','regular_price', 'expriy_date', 'image', 'quantity', 'commun_info', 'category_id', 'views'];
    protected $softDelete = true;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /*     public function getRouteKeyName()
    {
        return 'slug';
    } */
    public function getImageAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return asset("storage/{$value}");
    }
}
