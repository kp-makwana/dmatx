<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

// Spatie Packages

class User extends Authenticatable implements HasMedia
{
  use HasFactory,
    Notifiable,
    HasRoles,
    LogsActivity,
    InteractsWithMedia,
    HasTranslations;

  /**
   * Translatable fields
   */
  public array $translatable = ['name'];

  /**
   * Fillable attributes
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * Hidden
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Casts
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  /**
   * 🔥 Required by Spatie Activitylog v4+
   */
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly(['name', 'email'])
      ->useLogName('user')
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs();
  }

  /**
   * Media Collections
   */
  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('avatar')->singleFile();
  }
}
