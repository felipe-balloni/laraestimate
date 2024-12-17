<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'currency',
        'hourly_rate',
    ];

    protected $casts = [
        'hourly_rate' => MoneyCast::class,
    ];

    public static function createDefault(): array
    {
        return self::create([]);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile();
    }
}
