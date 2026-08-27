<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    public const BRAZILIAN_UF_CODES = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
        'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
        'SP', 'SE', 'TO',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'legal_name',
        'cnpj',
        'street',
        'number',
        'district',
        'city',
        'state',
        'zip_code',
        'complement',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone',
    ];

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $normalizedSearch = self::normalizeSearchTerm($search);

        if ($normalizedSearch === '') {
            return $query;
        }

        $escapedSearch = self::escapeLike($normalizedSearch);
        $documentSearch = self::digitsOnly($normalizedSearch);

        return $query->where(function (Builder $nestedQuery) use ($escapedSearch, $documentSearch) {
            $nestedQuery->whereRaw("legal_name LIKE ? ESCAPE '\\'", ["%{$escapedSearch}%"]);

            if ($documentSearch !== '') {
                if (strlen($documentSearch) === 14) {
                    $nestedQuery->orWhere('cnpj', $documentSearch);

                    return;
                }

                $nestedQuery->orWhere('cnpj', 'like', "%{$documentSearch}%");
            }
        });
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => preg_replace(
                '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
                '$1.$2.$3/$4-$5',
                $value,
            ),
        );
    }

    protected function zipCode(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $value),
        );
    }

    private static function normalizeSearchTerm(string $search): string
    {
        return preg_replace('/\s+/', ' ', trim($search)) ?? '';
    }

    private static function digitsOnly(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }

    private static function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }
}
