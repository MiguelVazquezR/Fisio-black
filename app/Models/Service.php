<?php

namespace App\Models;

use App\Enums\ServiceCategory;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property ServiceCategory $category
 * @property string|null $price
 * @property int $duration_minutes
 * @property string $description
 * @property array<int, string>|null $benefits
 * @property bool $is_active
 */
#[Fillable(['slug', 'name', 'category', 'price', 'duration_minutes', 'description', 'benefits', 'is_active'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ServiceCategory::class,
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'benefits' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
