<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPrice extends Model
{
    protected $table = 'asset_price';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function asset() {
        return $this->belongsTo(Asset::class);
    }
}
