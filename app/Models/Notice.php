<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    // Specification: Notice Title, Content, Date, Audience
    protected $fillable = [
        'title',
        'content',
        'publish_date',
        'target_audience' // 'all', 'teachers', 'parents', 'students'
    ];

    // Date casting taake format karna asaan ho
    protected $casts = [
        'publish_date' => 'date',
    ];
}