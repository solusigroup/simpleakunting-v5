<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedAssetDepreciation extends Model
{
    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(FixedAsset::class, 'aset_id');
    }
}
