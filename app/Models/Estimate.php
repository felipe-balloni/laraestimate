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
        'time_rate',
        'currency_symbol',
        'currency_decimal_separator',
        'currency_thousands_separator',
        'allows_to_select_items',
        'password',
    ];

    protected $appends = [
        'share_url',
        'logo_image',
        'currency_settings',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)
            ->with('items')
            ->orderBy('position')
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

    protected function shareUrl(): Attribute
    {
        return Attribute::make(
            route('estimates.view', $this),
        );
    }

    public function getCurrencySettingsAttribute(): array
    {
        $setting = Setting::first();

        return [
            'symbol' => $this->currency_symbol ?? optional($setting)->currency_symbol,
            'decimal_separator' => $this->currency_decimal_separator ?? optional($setting)->currency_decimal_separator,
            'thousands_separator' => $this->currency_thousands_separator ?? optional($setting)->currency_thousands_separator,
        ];
    }

    public function getNextSectionPosition(): int
    {
        return $this->sections()->max('position') + 1;
    }

    public function saveSectionsPositions(?array $positions): void
    {
        if(empty($positions)) return;

        foreach ($positions as $sectionId => $position) {
            $section = Section::find($sectionId);

            if($section) {
                $section->position = $position;
                $section->save();
            }
        }
    }

    public function duplicate(): bool
    {
        $estimateData = $this->treatDataForDuplication(
            $this->toArray()
        );

        $estimateData['name'] = $estimateData['name'] . ' Copy';
        $duplicated = self::create($estimateData);

        $this->copySectionsTo($duplicated);

        return $duplicated;
    }

    protected function copySectionsTo(Estimate $duplicated): void
    {
        $this->sections->each(function($section) use ($duplicated) {
            $sectionData = $this->treatDataForDuplication(
                $section->toArray()
            );

            $newSection = $duplicated->sections()->create($sectionData);
            $this->copySectionItems($section, $newSection);
        });
    }

    protected function copySectionItems(Section $from, Section $to): void
    {
        $from->items->each(function($item) use ($to) {
            $itemData = $this->treatDataForDuplication(
                $item->toArray()
            );

            $to->items()->create($itemData);
        });
    }

    protected function treatDataForDuplication(array $data): array
    {
        $removeKeys = ['id', 'created_at', 'updated_at', 'password'];

        return array_diff_key($data, array_flip($removeKeys));
    }
}
