<?php

namespace ArtisanWebLab\Spatial;

use ArtisanWebLab\GeoJson\GeoJson;
use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\GeoJson\Geometry\Polygon;
use Illuminate\Database\DatabaseManager as IlluminateDatabaseManager;
use Illuminate\Support\Facades\DB;

class DatabaseManager extends IlluminateDatabaseManager
{
    public function geometryRaw(string $query): ?Geometry
    {
        $select = sprintf('ST_AsGeoJson(%s) as response', $query);

        if ($response = DB::query()->selectRaw($select)->first()) {
            return GeoJson::jsonUnserialize($response->response);
        }

        return null;
    }

    /**
     * X, Y, Z are parameters for the following postgis equation.
     * The default values are chosen according to the size of the
     * original geometry to give a slightly bigger geometry,
     * without too many nodes.
     *
     * Note that:
     * X > 0 will give a polygon bigger than the original geometry, and guaranteed to contain it.
     * X = 0 will give a polygon similar to the original geometry.
     * X < 0 will give a polygon smaller than the original geometry, and guaranteed to be smaller.
     *
     * @param  Polygon  $polygon
     * @param  float    $x
     * @param  float    $y
     * @param  float    $z
     *
     * @return null|Geometry
     */
    public function simplifiedPolygon(Polygon $polygon, float $x, float $y, float $z): ?Geometry
    {
        $buffer = "ST_Buffer($polygon, $x)";
        $snapToGrid = "ST_SnapToGrid($buffer, $y)";
        $simplifyPreserveTopology = "ST_SimplifyPreserveTopology($snapToGrid, $z)";
        $simplifyBuffer = "ST_Buffer($simplifyPreserveTopology)";

        if ($x > 0) {
            return $this->geometryRaw("ST_Union(ST_MakeValid(ST_SimplifyPreserveTopology($polygon, 0.00001)), $simplifyBuffer)");
        } elseif ($x == 0) {
            return $this->geometryRaw($simplifyBuffer);
        } elseif ($x < 0) {
            return $this->geometryRaw("ST_Intersection($polygon, $simplifyBuffer)");
        }
    }
}
