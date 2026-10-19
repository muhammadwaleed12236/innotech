<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerFieldDefinition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_field_definitions';

    protected $fillable = [
        'field_key',
        'label',
        'field_type',
        'is_system',
        'system_column',
        'section',
        'default_value',
        'placeholder',
        'help_text',
        'options',
        'is_required',
        'is_unique',
        'is_enabled',
        'is_readonly',
        'visible_in_list',
        'visible_in_view',
        'visible_in_create',
        'visible_in_edit',
        'visible_in_search',
        'visible_in_export',
        'sort_order',
        'view_permission',
        'edit_permission',
    ];

    protected $casts = [
        'is_system'          => 'boolean',
        'is_required'        => 'boolean',
        'is_unique'          => 'boolean',
        'is_enabled'         => 'boolean',
        'is_readonly'        => 'boolean',
        'visible_in_list'    => 'boolean',
        'visible_in_view'    => 'boolean',
        'visible_in_create'  => 'boolean',
        'visible_in_edit'    => 'boolean',
        'visible_in_search'  => 'boolean',
        'visible_in_export'  => 'boolean',
        'sort_order'         => 'integer',
        'options'            => 'array',
    ];

    public function customValues()
    {
        return $this->hasMany(CustomerCustomFieldValue::class, 'field_definition_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Check if user has permission to view this field
     */
    public function canView($user = null): bool
    {
        if (empty($this->view_permission)) {
            return true;
        }

        $user = $user ?: auth()->user();
        if (!$user) {
            return false;
        }

        // Admin/Super Admin override if exists
        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('Admin'))) {
            return true;
        }

        return method_exists($user, 'can') ? $user->can($this->view_permission) : true;
    }

    /**
     * Check if user has permission to edit this field
     */
    public function canEdit($user = null): bool
    {
        if (empty($this->edit_permission)) {
            return true;
        }

        $user = $user ?: auth()->user();
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('Admin'))) {
            return true;
        }

        return method_exists($user, 'can') ? $user->can($this->edit_permission) : true;
    }

    /**
     * Format options array for select dropdowns
     */
    public function getParsedOptions(): array
    {
        if (empty($this->options)) {
            return [];
        }

        $opts = $this->options;
        if (is_string($opts)) {
            $decoded = json_decode($opts, true);
            $opts = is_array($decoded) ? $decoded : explode("\n", str_replace("\r", "", $opts));
        }

        $result = [];
        foreach ($opts as $key => $val) {
            if (is_array($val)) {
                $optVal = $val['key'] ?? $val['value'] ?? $key;
                $optLabel = $val['label'] ?? $val['name'] ?? $optVal;
                $result[$optVal] = $optLabel;
            } elseif (is_string($key) && !is_numeric($key)) {
                $result[$key] = $val;
            } else {
                $clean = trim((string)$val);
                if ($clean !== '') {
                    $result[$clean] = $clean;
                }
            }
        }

        return $result;
    }
}
