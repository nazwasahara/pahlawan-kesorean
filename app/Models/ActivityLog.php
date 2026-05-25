<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_identity',
        'action',
        'description',
    ];

    /**
     * Helper to log an activity.
     */
    public static function log(string $action, string $description, ?string $userIdentity = null)
    {
        $userId = auth()->id();

        if ($userIdentity === null) {
            $userIdentity = 'Sistem';

            if (auth()->check()) {
                $role = auth()->user()->role;
                $userIdentity = ucfirst($role);
            }
        }

        return self::create([
            'user_id' => $userIdentity === 'Sistem' ? null : $userId,
            'user_identity' => $userIdentity,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
