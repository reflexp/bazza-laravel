<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomenclatures extends Model
{
    use HasFactory;

    protected $fillable = ['article'];
    protected $columns = ['id', 'article', 'supplierArticle', 'supplierID', 'title', 'additionText', 'manufacturer', 'price', 'amount', 'active', 'deleted', 'createdBy', 'updatedBy', 'updated_at', 'created_at'];

     public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
