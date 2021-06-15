<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    protected $columns = ['id', 'name', 'login', 'email', 'buyerType', 'password', 'sessionHash', 'active', 'deleted', 'updated_at', 'created_at'];

    protected $fillable = ['password'];

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
