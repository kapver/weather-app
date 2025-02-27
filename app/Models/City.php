<?php

namespace App\Models;

use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * Set the coordinates attribute by converting the given array
     * of longitude and latitude into a PostGIS geography point.
     *
     * @param array $coordinates An array containing longitude and latitude values.
     * @return void
     */
    public function setCoordinatesAttribute(array $coordinates): void
    {
        $longitude = $coordinates[0] ?? $coordinates['longitude'];
        $latitude  = $coordinates[1] ?? $coordinates['longitude'];

        if (!$longitude || !$latitude) {
            return;
        }

        $this->attributes['coordinates'] = DB::raw("ST_SetSRID(ST_MakePoint($longitude, $latitude), 4326)");
    }

    /**
     * Get the coordinates attribute by extracting the longitude and latitude
     * from the PostGIS geography point.
     *
     * @param mixed $value The geography point value stored in the database.
     * @return ?object An object containing 'longitude' and 'latitude' properties.
     */
    public function getCoordinatesAttribute($value): ?object
    {
        return DB::selectOne("SELECT ST_X(?) AS longitude, ST_Y(?) AS latitude", [$value, $value]);
    }

}