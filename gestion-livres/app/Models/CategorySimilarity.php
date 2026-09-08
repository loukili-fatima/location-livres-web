<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorySimilarity extends Model
{
    protected $fillable = [
        'category_id',
        'categorie_proche_id',
        'score',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categorieProche()
    {
        return $this->belongsTo(Category::class, 'categorie_proche_id');
    }
}
