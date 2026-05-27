<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'user_name', 'action', 'subject_type', 'subject_id', 'subject_title',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $subjectType, ?int $subjectId, string $subjectTitle): void
    {
        static::create([
            'user_id'       => auth()->id(),
            'user_name'     => auth()->user()?->name ?? 'Unknown',
            'action'        => $action,
            'subject_type'  => $subjectType,
            'subject_id'    => $subjectId,
            'subject_title' => $subjectTitle,
        ]);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'created'      => 'Created',
            'updated'      => 'Updated',
            'deleted'      => 'Soft Deleted',
            'restored'     => 'Restored',
            'force_deleted' => 'Permanently Deleted',
            default        => ucfirst($this->action),
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            'created'       => 'text-green-600 dark:text-green-400',
            'updated'       => 'text-blue-600 dark:text-blue-400',
            'deleted'       => 'text-orange-500 dark:text-orange-400',
            'restored'      => 'text-teal-600 dark:text-teal-400',
            'force_deleted' => 'text-red-600 dark:text-red-400',
            default         => 'text-gray-500',
        };
    }
}
