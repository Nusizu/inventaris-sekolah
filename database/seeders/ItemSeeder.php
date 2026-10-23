<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Alat Lab', 'Mikroskop Binokuler', 'LAB-001', 5, 'baik'],
            ['Alat Lab', 'Gelas Ukur 100 ml', 'LAB-002', 20, 'baik'],
            ['Alat Lab', 'Tabung Reaksi', 'LAB-003', 50, 'rusak ringan'],
            ['Alat Olahraga', 'Bola Basket', 'OLR-001', 8, 'baik'],
            ['Alat Olahraga', 'Raket Badminton', 'OLR-002', 12, 'baik'],
            ['Alat Olahraga', 'Bola Voli', 'OLR-003', 6, 'rusak ringan'],
            ['Alat Olahraga', 'Matras Senam', 'OLR-004', 10, 'baik'],
            ['Elektronik', 'Proyektor Epson', 'ELK-001', 3, 'baik'],
            ['Elektronik', 'Laptop Acer', 'ELK-002', 10, 'baik'],
            ['Elektronik', 'Speaker Portable', 'ELK-003', 2, 'rusak berat'],
        ];

        foreach ($items as [$category, $name, $code, $stock, $condition]) {
            Item::create([
                'category_id' => Category::where('name', $category)->value('id'),
                'name' => $name,
                'code' => $code,
                'stock' => $stock,
                'condition' => $condition,
            ]);
        }
    }
}