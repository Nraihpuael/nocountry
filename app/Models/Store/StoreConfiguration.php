<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'primary_color',
        'secondary_color',
        'background_color',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
