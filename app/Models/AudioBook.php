<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Rating;
use App\Models\Report;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AudioBook extends Model
{
    use HasFactory;

    /**
     * ▼▼▼ هذا هو التعديل الأول ▼▼▼
     * تم حذف 'category' وإضافة 'category_id'
     */
    // هذا هو الكود الصحيح 1000%
protected $fillable = [
    'title',
    'author',
    'narrator',
    'description',
    'duration',
    'language',
    'publisher_id',
    'category_id',        // <-- **إصلاح:** تم استخدام الاسم الصحيح
    'status',             // <-- **إصلاح:** تم إضافة حقل الحالة
    'cover_image_path',   // <-- **إصلاح:** تم استخدام الاسم الصحيح
    'file_path',
    'pdf_path',
];


    /**
     * العلاقة: الكتاب ينتمي إلى مستخدم (الناشر)
     */
    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    /**
     * ▼▼▼ هذا هو التعديل الثاني ▼▼▼
     * العلاقة الجديدة: الكتاب ينتمي إلى فئة واحدة
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * علاقة الكتاب مع التقييمات (Ratings).
     * الكتاب الواحد يمكن أن يكون لديه العديد من التقييمات.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * علاقة الكتاب مع الإبلاغات (Reports).
     * الكتاب الواحد يمكن أن يكون لديه العديد من الإبلاغات.
     */
    public function reports()
    {
        // ملاحظة: هذه علاقة متعددة الأشكال (Polymorphic)
        return $this->morphMany(Report::class, 'reportable');
    }

    // --- باقي الدوال كما هي ---
    public function playlist() { return $this->belongsTo(Playlist::class); }
    public function averageRating() { return $this->ratings->avg('rating'); }
    public function ratingsCount() { return $this->ratings->count(); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function bookmarkedBy() { return $this->belongsToMany(User::class, 'bookmarks', 'audio_book_id', 'listener_id'); }
    public function downloads() { return $this->hasMany(Download::class); }
}
