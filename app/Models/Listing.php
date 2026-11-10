<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'category_id', 'title', 'price', 'item_condition', 'description', 'location', 'status', 'views_count'])]
class Listing extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'views_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function cardData(): array
    {
        $image = $this->images->first();

        $phone = $this->relationLoaded('user') ? $this->user?->phone : null;
        $whatsappUrl = $this->status === 'available' && is_string($phone)
            && preg_match('/^62[1-9][0-9]{7,12}$/', $phone)
                ? 'https://wa.me/'.$phone.'?text='.rawurlencode('Halo, pakde! saya tertarik dengan '.$this->title.' (#'.$this->id.')., saya mau liat detailnya dong...')
                : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'item_condition' => $this->item_condition,
            'status' => $this->status,
            'location' => $this->location,
            'views_count' => (int) ($this->views_count ?? 0),
            'image_url' => $image ? Storage::disk('public')->url($image->path) : null,
            'images' => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => Storage::disk('public')->url($img->path),
            ])->values()->all(),
            'images_count' => $this->images->count(),
            'category' => $this->category ? $this->category->only('id', 'name', 'slug') : null,
            'whatsapp_url' => $whatsappUrl,
        ];
    }
}
