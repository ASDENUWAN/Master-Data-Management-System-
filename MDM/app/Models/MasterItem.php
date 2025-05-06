<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_id',
        'category_id',
        'code',
        'name',
        'attachment',
        'status',
    ];

    public function brand()
    {
        return $this->belongsTo(MasterBrand::class);
    }

    public function category()
    {
        return $this->belongsTo(MasterCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
