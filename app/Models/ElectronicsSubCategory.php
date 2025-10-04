<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicsSubCategory extends Model
{
    use HasFactory;

    protected $table = 'electronics_sub_category';

    public $fillable = [
        'electronics_category_id',
        'subCategory_name',
        'subCategory_photo',
        'subCategory_price',
        'subCategory_status',
    ];
    public function refersCategoryElect(){
        return $this->belongsTo(ElectronicsCategory::class, 'electronics_category_id');
    }
}
