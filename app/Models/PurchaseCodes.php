<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCodes extends Model
{
    use HasFactory;
    // protected $table = 'Purchase_codes';

    protected $fillable = [
        'user_id',
        'purchase_code',
        'url',
    ];    

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function subscription(){
        return $this->hasMany(Subscriptions::class);
    }
}
