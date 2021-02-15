<?php

namespace ArtisanWebLab\Spatial;

use ArtisanWebLab\GeoJson\GeoJson;
use ArtisanWebLab\GeoJson\Geometry\Geometry;
use Illuminate\Database\DatabaseManager as IlluminateDatabaseManager;
use Illuminate\Support\Facades\DB;

class DatabaseManager extends IlluminateDatabaseManager
{
    public function geometryRaw(string $query): null|Geometry
    {
        $select = sprintf('ST_AsGeoJson(%s) as response', $query);

        if ($response = DB::query()->selectRaw($select)->first()) {
            return GeoJson::jsonUnserialize($response->response);
        }

        return null;
    }
}
