<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property array $settings User settings stored as JSON.
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the associated user settings.
     *
     * @return HasOne
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the cities that belong to the user.
     *
     * @return BelongsToMany
     */
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'user_city');
    }

    /**
     * Get the settings as an array.
     *
     * @return array|null
     */
    public function getSettingsArrayAttribute()
    {
        return $this->settings?->settings;
    }

    /**
     * Get the notification channels for the user.
     *
     * @return array<int, string>
     */
    public function getNotificationChannelsAttribute(): array
    {
        $channels = [];

        $email_enabled    = data_get($this->settings, 'settings.weather.email_enabled');
        $telegram_enabled = data_get($this->settings, 'settings.weather.telegram_enabled');
        $telegram_chat_id = data_get($this->settings, 'settings.weather.telegram_chat_id');

        if ($email_enabled) {
            $channels[] = 'mail';
        }

        if ($telegram_enabled && $telegram_chat_id) {
            $channels[] = 'telegram';
        }

        return $channels;
    }
}
