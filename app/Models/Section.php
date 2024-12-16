<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasUuids, HasFactory;

    const TYPE_TEXT = "text";
    const TYPE_PRICES = "prices";

    protected $fillable = [
        'estimate_id',
        'text',
        'type',
        'order',
        'sort'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    protected $appends = [
        'presentable_text',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($section) {
            $section->order = ($section->estimate?->sections()->max('order') ?? 0) + 1;
        });
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class)
            ->orderBy('order');
    }

    public function getPresentableTextAttribute(): string
    {
        $text = $this->text;

        $text = str_replace('*TOTAL_PRICE*', '<span class="total-calc-price"></span>', $text);
        $text = str_replace('*TOTAL_SELECTED_PRICE*', '<span class="total-selected-calc-price"></span>', $text);

        return $text;
    }
}
