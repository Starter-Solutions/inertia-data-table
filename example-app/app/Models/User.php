<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\DataTableSorts\NameLengthSort;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use StarterSolutions\InertiaDataTable\Attributes\AllowedSorts;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
#[AllowedSorts(['id', 'name', 'display_name' => 'name', 'name_length' => NameLengthSort::class, 'email', 'email_verified_at', 'created_at'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $appends = ['display_name', 'name_length'];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => strtoupper($this->name));
    }

    protected function nameLength(): Attribute
    {
        return Attribute::get(fn (): int => mb_strlen($this->name));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
