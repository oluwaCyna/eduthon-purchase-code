<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryRedirectUrl extends Model
{
    use HasFactory;
    protected $fillable = [
        'ref',
        'url'
    ];    
}
