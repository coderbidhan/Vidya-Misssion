<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupersededDeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_id',
        'superseded_at',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token_id' => 'integer',
            'superseded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * User associated with the superseded token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
