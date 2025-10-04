<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicsCategory extends Model
{
    use HasFactory;

    protected $table = 'electronics_category';

    protected $fillable = [
        'electronic_id',
        'category_name',
        'category_photo',
        'category_status',
    ];
    public function refersSubCategoryElect(){
        return $this->hasMany(ElectronicsSubCategory::class, 'electronics_category_id');
    }

    public function refersElectronics(){
        return $this->belongsTo(Electronics::class, 'electronic_id');
    }
}
