<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetGroup extends Model
{
    protected $guarded = [];

    public function assets()
    {
        return $this->hasMany(FixedAsset::class, 'kelompok_aset_id');
    }
}
