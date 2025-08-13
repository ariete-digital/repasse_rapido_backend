<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $locations = [
            'all',
            'header_home',
            'horizontal_highlights_home_top',
            'horizontal_highlights_home_middle',
            'horizontal_highlights_home_bottom',
            'vertical_highlights_home',
        ];

        $user = User::find(1);

        foreach ($locations as $location) {
            for ($i = 1; $i <= 3; $i++) {
                $state = null;
                $city = null;
                $banner = \App\Models\Banner::firstOrCreate(
                    [
                        'filename' => "https://img.freepik.com/vetores-gratis/carro-esportivo-azul-isolado-no-branco-vector_53876-67354.jpg",
                        'title' => $faker->sentence(3),
                        'subtitle' => $faker->sentence(5),
                        'link' => 'https://www.diegoborgs.com.br',
                        'created_by_user' => $user->id,
                        'starts_at' => $faker->dateTimeBetween('-1 month', 'now')
                    ]
                );

                $bannerLocation = \App\Models\BannerLocation::firstOrCreate(
                    ['location_key' => $location],
                    ['created_by_user' => $user->id]
                );

                if ($i % 2 == 0) {
                    $state = \App\Models\Uf::inRandomOrder()->first();
                    $banner->states()->attach($state);
                    if ($i % 3 == 0) {
                        $city = \App\Models\Cidade::inRandomOrder()->first();
                        $banner->cities()->attach($city);
                    }
                }

                $banner->locations()->attach($bannerLocation);
            }
        }
    }
}
