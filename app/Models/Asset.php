<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'asset';
    protected $guarded = ['id'];
    public $timestamps = false;
    


    public function asset_price() {
        return $this->hasMany(AssetPrice::class);
    }
    

}
