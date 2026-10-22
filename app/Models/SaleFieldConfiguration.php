<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SaleFieldConfiguration extends Model
{
    use HasFactory;

    protected $table = 'sale_field_configurations';

    protected $fillable = [
        'field_key',
        'label',
        'section',
        'field_type',
        'description',
        'default_value',
        'is_enabled',
        'visible_in_create',
        'visible_in_edit',
        'visible_in_print',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled'        => 'boolean',
        'visible_in_create' => 'boolean',
        'visible_in_edit'   => 'boolean',
        'visible_in_print'  => 'boolean',
        'is_system'         => 'boolean',
        'sort_order'        => 'integer',
    ];

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Get all configurations cached as key => model
     */
    public static function getAllCached()
    {
        return Cache::remember('sale_field_configurations_all', 3600, function () {
            return self::sorted()->get()->keyBy('field_key');
        });
    }

    /**
     * Check if a field or feature is enabled
     */
    public static function isEnabled(string $fieldKey, bool $default = true): bool
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey])) {
            return (bool) $all[$fieldKey]->is_enabled;
        }
        return $default;
    }

    /**
     * Check if field is visible in create
     */
    public static function isVisibleInCreate(string $fieldKey, bool $default = true): bool
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey])) {
            return (bool) ($all[$fieldKey]->is_enabled && $all[$fieldKey]->visible_in_create);
        }
        return $default;
    }

    /**
     * Check if field is visible in edit
     */
    public static function isVisibleInEdit(string $fieldKey, bool $default = true): bool
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey])) {
            return (bool) ($all[$fieldKey]->is_enabled && $all[$fieldKey]->visible_in_edit);
        }
        return $default;
    }

    /**
     * Check if field is visible in print
     */
    public static function isVisibleInPrint(string $fieldKey, bool $default = true): bool
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey])) {
            return (bool) ($all[$fieldKey]->is_enabled && $all[$fieldKey]->visible_in_print);
        }
        return $default;
    }

    /**
     * Get configured value (e.g. default tax rate)
     */
    public static function getValue(string $fieldKey, $default = null)
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey])) {
            return $all[$fieldKey]->default_value ?? $default;
        }
        return $default;
    }

    /**
     * Get tax rate as float
     */
    public static function getTaxRate(string $fieldKey, float $default = 0.0): float
    {
        $all = self::getAllCached();
        if (isset($all[$fieldKey]) && $all[$fieldKey]->is_enabled) {
            return (float) ($all[$fieldKey]->default_value ?? $default);
        }
        return 0.0;
    }

    /**
     * Clear configuration cache
     */
    public static function clearCache(): void
    {
        Cache::forget('sale_field_configurations_all');
        Cache::forget('sale_field_config_bundle');
    }

    /**
     * Get simple associative config bundle for blade & frontend scripts
     */
    public static function getConfigBundle(): array
    {
        return Cache::remember('sale_field_config_bundle', 3600, function () {
            $all = self::sorted()->get();
            $bundle = [
                'flags' => [],
                'values' => [],
                'create_visible' => [],
                'edit_visible' => [],
                'print_visible' => [],
            ];

            foreach ($all as $item) {
                $bundle['flags'][$item->field_key] = (bool) $item->is_enabled;
                $bundle['values'][$item->field_key] = $item->default_value;
                $bundle['create_visible'][$item->field_key] = (bool) ($item->is_enabled && $item->visible_in_create);
                $bundle['edit_visible'][$item->field_key] = (bool) ($item->is_enabled && $item->visible_in_edit);
                $bundle['print_visible'][$item->field_key] = (bool) ($item->is_enabled && $item->visible_in_print);
            }

            // Specific tax convenience
            $salesTaxEnabled = $bundle['flags']['sales_tax'] ?? true;
            $furtherTaxEnabled = $bundle['flags']['further_tax'] ?? true;
            $bundle['default_st_rate'] = $salesTaxEnabled ? (float) ($bundle['values']['sales_tax'] ?? 18) : 0;
            $bundle['default_ft_rate'] = $furtherTaxEnabled ? (float) ($bundle['values']['further_tax'] ?? 3) : 0;
            $bundle['strn_exemption'] = $bundle['flags']['strn_exemption'] ?? true;

            return $bundle;
        });
    }
}
