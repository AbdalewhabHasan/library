<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'status',
    ];

    /**
     * العلاقة: الإبلاغ ينتمي إلى مستخدم (المُبلِغ).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة: الإبلاغ ينتمي إلى كيان قابل للإبلاغ (كتاب، تعليق، إلخ).
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
