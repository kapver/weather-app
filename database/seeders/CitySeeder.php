<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = 'cities.csv';

        if (!Storage::exists($csvPath)) {
            return;
        }

        $csv = Reader::createFromPath(Storage::path($csvPath), 'r');
        $csv->setHeaderOffset(0);

        // "capital" field possible values: 'primary' - capital, 'admin' - state, 'minor' - county, '' - town
        $cityFilterFunc = function ($city) {
            return app()->isProduction() || in_array($city['capital'], ['primary', 'admin', 'minor']);
        };

        collect($csv)
            ->filter($cityFilterFunc)
            ->chunk(1000)
            ->each(function ($chunk) {
                $data = $chunk->map(function ($row) {
                    $latitude  = (float)$row['lat'];
                    $longitude = (float)$row['lng'];
                    return [
                        'type' => $row['capital'],
                        'name' => $row['city'],
                        'country' => $row['country'],
                        'coordinates' => DB::raw("ST_SetSRID(ST_MakePoint($longitude, $latitude), 4326)"),
                    ];
                })->toArray();

                DB::table('cities')->insert($data);
            });
    }
}