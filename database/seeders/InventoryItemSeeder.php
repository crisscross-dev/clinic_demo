<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryItem;
use Illuminate\Support\Str;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            // Pain & Fever
            'Paracetamol 500mg',
            'Ibuprofen 400mg',
            'Mefenamic Acid 500mg',

            // Allergy
            'Cetirizine 10mg',
            'Loratadine 10mg',
            'Diphenhydramine 25mg',

            // Gastrointestinal
            'Antacid 500mg',
            'Loperamide 2mg',
            'Domperidone 10mg',
            'Metoclopramide 10mg',

            // Vitamins / Supplements
            'Vitamin C 500mg',
            'Multivitamins tablet',

            // Others
            'Aspirin 81mg',
            'Sodium Bicarbonate 325mg',
            'Buscopan 10mg',
        ];




        foreach ($names as $i => $name) {
            $total = rand(0, 200);
            $reminder = rand(3, 15);

            // Determine status using the same labels as the DB enum
            if ($total <= 0) {
                $status = 'Out of Stock';
            } elseif ($total < $reminder) {
                $status = 'Low Stock';
            } else {
                $status = 'In Stock';
            }

            InventoryItem::create([
                'name' => $name,
                'category_id' => 1,
                'total_stock' => $total,
                'low_stock_reminder' => $reminder,
                'status' => $status,
            ]);
        }
    }
}

// php artisan db:seed --class=InventoryItemSeeder