<?php

namespace App\Services;

use App\Models\SaleFieldConfiguration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaleFieldService
{
    const CACHE_KEY = 'sale_field_configurations_all';
    const CACHE_BUNDLE_KEY = 'sale_field_config_bundle';
    const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all sale field configurations
     */
    public function getAllConfigurations(bool $refresh = false)
    {
        if ($refresh) {
            $this->clearCache();
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            if (SaleFieldConfiguration::count() === 0) {
                $this->seedDefaults();
            }
            return SaleFieldConfiguration::sorted()->get();
        });
    }

    /**
     * Get configurations grouped by section
     */
    public function getGroupedConfigurations()
    {
        return $this->getAllConfigurations()->groupBy('section');
    }

    /**
     * Get quick lookup bundle for views and scripts
     */
    public function getConfigBundle(): array
    {
        return SaleFieldConfiguration::getConfigBundle();
    }

    /**
     * Toggle a specific boolean attribute of a field definition
     */
    public function toggleAttribute(int $id, string $attribute): SaleFieldConfiguration
    {
        $field = SaleFieldConfiguration::findOrFail($id);
        
        $allowed = ['is_enabled', 'visible_in_create', 'visible_in_edit', 'visible_in_print'];
        if (in_array($attribute, $allowed)) {
            $field->{$attribute} = !$field->{$attribute};
            $field->save();
            $this->clearCache();
        }

        return $field;
    }

    /**
     * Update field details & values
     */
    public function updateField(int $id, array $data): SaleFieldConfiguration
    {
        $field = SaleFieldConfiguration::findOrFail($id);
        $field->update($data);
        $this->clearCache();

        return $field;
    }

    /**
     * Bulk update field values & visibility
     */
    public function updateBulk(array $items): void
    {
        foreach ($items as $id => $payload) {
            $field = SaleFieldConfiguration::find($id);
            if ($field) {
                $updateData = [];
                if (isset($payload['label'])) {
                    $updateData['label'] = $payload['label'];
                }
                if (array_key_exists('default_value', $payload)) {
                    $updateData['default_value'] = $payload['default_value'];
                }
                if (isset($payload['is_enabled'])) {
                    $updateData['is_enabled'] = (bool) $payload['is_enabled'];
                }
                if (isset($payload['visible_in_create'])) {
                    $updateData['visible_in_create'] = (bool) $payload['visible_in_create'];
                }
                if (isset($payload['visible_in_edit'])) {
                    $updateData['visible_in_edit'] = (bool) $payload['visible_in_edit'];
                }
                if (isset($payload['visible_in_print'])) {
                    $updateData['visible_in_print'] = (bool) $payload['visible_in_print'];
                }
                if (isset($payload['sort_order'])) {
                    $updateData['sort_order'] = (int) $payload['sort_order'];
                }

                if (!empty($updateData)) {
                    $field->update($updateData);
                }
            }
        }

        $this->clearCache();
    }

    /**
     * Reset all sale configurations back to system defaults
     */
    public function resetDefaults(): void
    {
        SaleFieldConfiguration::truncate();
        $this->seedDefaults();
        $this->clearCache();
    }

    /**
     * Clear all caches
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_BUNDLE_KEY);
        SaleFieldConfiguration::clearCache();
    }

    /**
     * Seed initial default settings
     */
    protected function seedDefaults(): void
    {
        $now = now();
        $defaults = [
            // Taxes & Legal Engine
            [
                'field_key' => 'sales_tax',
                'label' => 'Sales Tax (ST / GST)',
                'section' => 'Taxes & Legal Details',
                'field_type' => 'tax',
                'description' => 'Enable or disable Sales Tax (ST %) across the sale screen, line calculations, and invoice prints.',
                'default_value' => '18.00',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'further_tax',
                'label' => 'Further Tax (FT)',
                'section' => 'Taxes & Legal Details',
                'field_type' => 'tax',
                'description' => 'Enable or disable Further Tax (FT %) for unregistered buyers (under Sales Tax Act 1990).',
                'default_value' => '3.00',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'strn_exemption',
                'label' => 'STRN Auto-Exemption (0% FT)',
                'section' => 'Taxes & Legal Details',
                'field_type' => 'toggle',
                'description' => 'Automatically set Further Tax to 0% when the selected customer has an STRN registered.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'exclusive_gst_amount',
                'label' => 'Exclusive GST Calculation & Display',
                'section' => 'Taxes & Legal Details',
                'field_type' => 'tax',
                'description' => 'Calculate and show Exclusive GST Amount (Amount after line discount before tax).',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'customer_tax_badges',
                'label' => 'Customer Tax Badges (NTN/STRN)',
                'section' => 'Taxes & Legal Details',
                'field_type' => 'toggle',
                'description' => 'Display registered STRN/NTN badges or unregistered tax status notice below customer select.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Item Grid Columns
            [
                'field_key' => 'col_stock',
                'label' => 'Stock Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show available stock pieces column in the sale item grid.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 110,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_size',
                'label' => 'Size / Variant Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show size / pack specification column in the items table.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 120,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_pieces',
                'label' => 'Pieces (Pcs) Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show calculated total pieces column in the items table.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 130,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_pcs_ctn',
                'label' => 'Pcs / Carton Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show pieces per carton packaging specification column.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 140,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_price_mode',
                'label' => 'Price Mode Toggle (Retail / Wholesale)',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show R/W button in price input to quickly switch between Retail and Wholesale price.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 150,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_discount',
                'label' => 'Item Discount Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show discount input (% and PKR toggle) on each line item.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 160,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_sales_tax',
                'label' => 'Item ST % Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show Sales Tax % input column in the items table grid.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 170,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'col_further_tax',
                'label' => 'Item FT % Column',
                'section' => 'Item Table Columns',
                'field_type' => 'column',
                'description' => 'Show Further Tax % input column in the items table grid.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 180,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Order Summary & Totals
            [
                'field_key' => 'summary_gross',
                'label' => 'Subtotal (Gross) Row',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display Gross Subtotal before line discounts in Order Summary card.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 210,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_line_discount',
                'label' => 'Line Discount Row',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display total line-item discount amount in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 220,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_exclusive_gst',
                'label' => 'Exclusive GST Amount Row',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display Exclusive GST Amount row in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 230,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_sales_tax',
                'label' => 'Sales Tax (ST) Row',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display total Sales Tax row in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 240,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_further_tax',
                'label' => 'Further Tax (FT) Row',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display total Further Tax row in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 250,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_overall_discount',
                'label' => 'Order Discount (Rs) Input',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display order-level discount input box in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 260,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'summary_freight',
                'label' => 'Freight Charges Input',
                'section' => 'Order Summary & Totals',
                'field_type' => 'summary',
                'description' => 'Display shipping/freight input with (+/-) toggle in Order Summary.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 270,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Header & Checkout Options
            [
                'field_key' => 'field_credit_days',
                'label' => 'Credit Days Input',
                'section' => 'Header & Checkout Options',
                'field_type' => 'field',
                'description' => 'Show Credit Days input field beside Payment Methods.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 310,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'field_reference',
                'label' => 'Reference / Remarks Input',
                'section' => 'Header & Checkout Options',
                'field_type' => 'field',
                'description' => 'Show Reference / Remarks text input beside Payment Methods.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 320,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'field_sale_type',
                'label' => 'Sale Type Toggle (Customer / Walk-in)',
                'section' => 'Header & Checkout Options',
                'field_type' => 'field',
                'description' => 'Show the toggle button to switch between Customer and Walk-in mode.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => false,
                'is_system' => true,
                'sort_order' => 330,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'field_key' => 'print_tax_breakdown',
                'label' => 'Print Tax & NTN/STRN Breakdown',
                'section' => 'Header & Checkout Options',
                'field_type' => 'print',
                'description' => 'Show GST, Further Tax, and Customer NTN/STRN rows on invoice and thermal prints.',
                'default_value' => '1',
                'is_enabled' => true,
                'visible_in_create' => true,
                'visible_in_edit' => true,
                'visible_in_print' => true,
                'is_system' => true,
                'sort_order' => 340,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('sale_field_configurations')->insert($defaults);
    }
}
