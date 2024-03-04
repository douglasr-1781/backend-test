<?php

namespace Database\Seeders;

use App\Models\RedirectModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedirectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RedirectModel::factory()
            ->count(13)
            ->create();
    }
}
