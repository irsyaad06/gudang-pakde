<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'sepeda' => 'Sepeda',
            'perkakas' => 'Perkakas',
            'hobi' => 'Hobi',
            'elektronik' => 'Elektronik',
            'rumah-tangga' => 'Rumah Tangga',
        ] as $slug => $name) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
