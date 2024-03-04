<?php

namespace Database\Seeders;

use App\Models\RedirectLogModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedirectLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RedirectLogModel::factory()
            ->count(13)
            ->create();
    }
}
