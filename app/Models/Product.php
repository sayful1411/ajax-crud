<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const PLACEHOLDER_IMAGE_PATH = 'images/placeholder.jpeg';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'price',
        'created_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
