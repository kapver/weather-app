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

    public function setCoordinatesAttribute(array $coordinates): void
    {
        $longitude = $coordinates[0] ?? $coordinates['longitude'];
        $latitude = $coordinates[1] ?? $coordinates['longitude'];

        if (!$longitude || !$latitude) {
            return;
        }

        $this->attributes['coordinates'] = DB::raw("ST_SetSRID(ST_MakePoint($longitude, $latitude), 4326)");
    }

    public function getCoordinatesAttribute($value)
    {
        return DB::selectOne("SELECT ST_X(?) AS longitude, ST_Y(?) AS latitude", [$value, $value]);
    }

}