<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'name',
        'starting_price',
        'current_price',
        'end_time',
        'description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_image')
                ->acceptsMimeTypes(['image/jpeg', 'image/png','image/webp'])
                ->singleFile();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function highestBid()
    {
        return $this->bids()->orderByDesc('amount')->first();
    }
}
