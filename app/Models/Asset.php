<?php

namespace App\Models;

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'internal_code',
        'serial_number',
        'category',
        'brand',
        'model',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'category' => AssetCategory::class,
        'status' => AssetStatus::class,
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class);
    }

    public function isAssignable(): bool
    {
        if ($this->status !== AssetStatus::Available) {
            return false;
        }

        if ($this->relationLoaded('assignment')) {
            return $this->assignment === null;
        }

        return ! $this->assignment()->exists();
    }

    public function scopeFilter(Builder $query, string $search, ?string $category): Builder
    {
        $normalizedSearch = trim($search);

        if ($normalizedSearch !== '') {
            $escapedSearch = addcslashes($normalizedSearch, '\\%_');

            $query->whereRaw("internal_code LIKE ? ESCAPE '\\'", ["%{$escapedSearch}%"]);
        }

        if ($category !== null && in_array($category, AssetCategory::values(), true)) {
            $query->where('category', $category);
        }

        return $query;
    }
}
