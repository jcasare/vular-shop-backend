<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Model::unguard();

        $this->call([
            \Database\Seeders\UserSeeder23Feb25::class,
            \Database\Seeders\CategorySeeder03Mar26::class,
            \Database\Seeders\ProductSeeder03Mar26::class,
        ]);

        Model::reguard();
    }
}
