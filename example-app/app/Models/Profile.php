<?php

namespace App\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use StarterSolutions\InertiaDataTable\Attributes\AllowedSorts;

#[AllowedSorts(['display_name', 'city', 'company'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'city',
        'company',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
