<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_module_id',
        'purchase_code_id',
        'interval',
        'status',
        'subscription_date',
        'expiry_date'
    ];    

    public function package_module(){
        return $this->belongsTo(PackageModules::class);
    }

    public function purchase_code(){
        return $this->belongsTo(PurchaseCodes::class);
    }
}
