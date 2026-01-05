<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ListeningHistory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscribers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AudioBook[] $audioBooks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Playlist[] $playlists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bookmark[] $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Download[] $downloads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ListeningHistory[] $listeningHistory
 */
class User extends Authenticatable
{
    use  HasFactory, Notifiable;

    /**
     * الحقول التي يمكن تعبئتها.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status', // **إصلاح:** تم إرجاع حقل الحالة ليتوافق مع قاعدة البيانات
    ];

    /**
     * الحقول التي يجب إخفاؤها.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع البيانات.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- العلاقات (Relationships) ---

    /**
     * علاقة لجلب الكتب الصوتية التي يملكها الناشر.
     */
    public function audioBooks(): HasMany
    {
        return $this->hasMany(AudioBook::class, 'publisher_id');
    }

    /**
     * علاقة لجلب الناشرين الذين يتابعهم المستمع.
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'listener_id', 'publisher_id')->withTimestamps();
    }

    /**
     * علاقة لجلب المستمعين الذين يتابعون الناشر.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'publisher_id', 'listener_id')->withTimestamps();
    }

    /**
     * علاقة لجلب قوائم التشغيل الخاصة بالمستمع.
     */
    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class, 'listener_id');
    }

    /**
     * علاقة لجلب الإشارات المرجعية الخاصة بالمستمع.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'listener_id');
    }
// ▼▼▼ هذا هو الشكل الصحيح والنهائي للدالة ▼▼▼
public function achievements()
{
    return $this->belongsToMany(Achievement::class, 'listener_achievements', 'listener_id', 'achievement_id')
                ->withPivot('unlocked_at') // <-- هذا السطر مهم
                ->withTimestamps();
}



    /**
     * علاقة لجلب التنزيلات الخاصة بالمستمع.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class, 'listener_id');
    }

    /**
     * علاقة لجلب سجل الاستماع الخاص بالمستمع.
     */
    public function listeningHistory(): HasMany
    {
        return $this->hasMany(ListeningHistory::class, 'listener_id');
    }
}
