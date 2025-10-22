<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jenisperangkat extends Model
{
    /** @use HasFactory<\Database\Factories\JenisperangkatFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function perangkat()
    {
        return $this->hasMany(detailperangkat::class, 'id');
    }
}
