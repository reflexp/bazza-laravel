<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $columns = ['id', 'clientID', 'contactPhone', 'totalSum', 'comment', 'deliveryType', 'deliveryToStorageID', 'deliveryAddressInCity', 'needPrePayment', 'paymentConfirmed', 'userPaymentSum', 'status', 'processedBy', 'updated_at', 'created_at'];

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
