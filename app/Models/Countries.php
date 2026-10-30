<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    protected $table = 'operation_countries';

    public function user()
    {
        return $this->hasOne(User::class, 'country', 'id');
    }
}
