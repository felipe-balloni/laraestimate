<?php

namespace App\Models;

use App\Models\Setting;
use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
    use SoftDeletes, HasUuids, HasFactory;

    protected $fillable = [
        'name',
        'use_name_as_title',
        'expiration_date',
        'duration_rate',
        'currency',
        'allows_to_select_items',
        'password',
    ];

    protected $appends = [
//        'share_url',
        'logo_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)
            ->with('items')
            ->orderBy('order')
            ->orderBy('created_at', 'desc');
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(Item::class, Section::class);
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where('name', 'like', "%{$search}%");
    }

    public function getLogoImageAttribute(): ?string
    {
        $setting = Setting::first();

        if($setting) {
            return $setting->getFirstMediaUrl('logo');
        }

        return null;
    }
}
