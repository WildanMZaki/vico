<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressedVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'download_id',
        'video_name',
        'status',
    ];
}
