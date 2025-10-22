<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class log_status extends Model
{
    /** @use HasFactory<\Database\Factories\LogStatusFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
