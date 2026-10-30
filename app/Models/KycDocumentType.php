<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycDocumentType extends Model
{
    use HasFactory;

    protected $table = 'kyc_document_type';
    protected $fillable = ['name'];

    public function kyc()
    {
        return $this->hasMany(Kyc::class);
    }
}
