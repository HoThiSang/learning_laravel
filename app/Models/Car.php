<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $table = 'cars';


    public function mf(){
        return $this->belongsTo('App\Models\Mf', 'mf_id','id');
    }

    public function create($description, $model, $product_on)
    {
        Car::table($this->table)->insert([
            'description' => $description,
            'model' => $model,
            'product_on' => $product_on
        ]);
    }
}
