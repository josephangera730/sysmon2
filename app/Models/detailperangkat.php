<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detailperangkat extends Model
{
    /** @use HasFactory<\Database\Factories\DetailperangkatFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function perangkat()
    {
        return $this->belongsto(jenisperangkat::class, 'jenisperangkat_id');
    }

     public function internet()
    {
        return $this->belongsto(perangkat::class, 'perangkat_id');
    }
}
