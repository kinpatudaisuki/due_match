<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Format;

class FormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $formats = ['オリジナル', 'アドバンス', '2ブロック', 'デュエパ', '殿堂ゼロ'];

        foreach ($formats as $format) {
            Format::create(['name' => $format]);
        }
    }

}
