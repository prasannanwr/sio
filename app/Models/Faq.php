<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'is_popular',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id', 'id');
    }
}
