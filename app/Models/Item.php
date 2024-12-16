<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Item extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'description',
        'duration',
        'duration_rate',
        'price',
        'order',
        'obligatory',
    ];

    protected $casts = [
        'obligatory' => 'boolean',
        'order' => 'integer'
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function($item) {
            $item->order = ($item->section?->items()->max('order') ?? 0) + 1;
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,        // Target model
            Estimate::class,    // Intermediate model
            'id',               // Foreign key on estimates table
            'id',               // Foreign key on users table
            'section_id',       // Local key on items table
            'user_id'           // Local key on estimates table
        )->join('sections', 'sections.id', '=', 'items.section_id');
    }
}
