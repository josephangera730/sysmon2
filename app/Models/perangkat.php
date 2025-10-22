<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class perangkat extends Model
{
    /** @use HasFactory<\Database\Factories\PerangkatFactory> */
    use HasFactory;

    protected $table = 'perangkats';

    protected $guarded = ['id'];

    public function jenisperangkat()
    {
        return $this->BelongsTo(jenisperangkat::class, 'jenisperangkat_id', 'id');
    }

    public function internet()
    {
        return $this->hasmany(perangkat::class, 'perangkat_id');
    }
}
