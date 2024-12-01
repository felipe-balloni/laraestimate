<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'description',
        'duration',
        'duration_rate',
        'price',
        'obligatory',
    ];

    protected $casts = [
        'obligatory' => 'boolean'
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function($item) {
            $item->position = $item->section->getNextItemPosition();
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
