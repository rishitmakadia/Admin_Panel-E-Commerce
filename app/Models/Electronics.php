<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electronics extends Model
{
    use HasFactory;

    protected $table = 'electronics';
    protected $fillable=[
        'electronics_category',
        'electronics_category_photo',
        'electronics_category_status',
    ];
//    public function refersCategoryElects(){
    public function categories(){
        return $this->hasMany(ElectronicsCategory::class,'electronic_id');
    }
}
