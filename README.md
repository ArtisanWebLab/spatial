# Laravel Spatial

```shell
composer require artisanweblab/spatial
```

## PostgreSQL and PostGIS

PostGIS is a spatial database extender for PostgreSQL object-relational database. It adds support for geographic objects allowing location queries to be run in SQL.

[PostGIS Official Documentation](https://postgis.net/documentation)

[Install PostGIS to your Web server](https://postgis.net/install/)

[Install PostGIS to your Docker container](https://github.com/postgis/docker-postgis)

## MySQL

MySQL is not currently supported.

## Integration with your project

### Prepare your table

Add fields with the required data type:

```php
$table->geometry('foo_bar');
$table->geometryCollection('foo_bar');

$table->lineString('foo_bar');
$table->multiLineString('foo_bar');

$table->point('foo_bar');
$table->multiPoint('foo_bar');

$table->polygon('foo_bar');
$table->multiPolygon('foo_bar');
```

### Prepare your model

```php
<?php

namespace App\Models;

use ArtisanWebLab\Spatial\Traits\SpatialTrait;
use ArtisanWebLab\GeoJson\Geometry\MultiPoint;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * @package App\Models
 * @property integer    $id
 * @property string     $name
 * @property MultiPoint $location
 */
class User extends Model
{
    use HasFactory, SpatialTrait;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'location',
    ];

    // Define spatial fields
    protected $spatial = [
        'location',
    ];
}
```

### Example of Input and Output spatial data

```php
<?php

use App\Models\User;
use ArtisanWebLab\GeoJson\Geometry\MultiPoint;
use ArtisanWebLab\GeoJson\Geometry\Point;

/** @var User $user */
$user = User::query()->first();

$longitude1 = 13.649029;
$latitude1 = 47.56189;

$longitude2 = 35.046636;
$latitude2 = 48.464486;

$point1 = [$longitude1, $latitude1];
$point2 = [$longitude2, $latitude2];

$user->location = new MultiPoint([
    new Point($point1),
    new Point($point2),
]);

$user->save();

//...

$user = User::query()
    ->first();

get_class($user->location); // GeoJson\Geometry\MultiPoint
```

### How to find records by Geometry

```php
use ArtisanWebLab\GeoJson\Geometry\LineString;

$lineString = new LineString([
    // array of points ...
]);

$users = User::query()
    ->withDistanceSphere('location', $lineString)
    ->whereDistanceSphere('location', $lineString, '<=', 1000) // 1000 meters
    ->get();
```

### Available Eloquent methods

```php
withDistance(string $column, Geometry $geometry, string $alias = null, bool $sphere = false): Builder

withDistanceSphere(string $column, Geometry $geometry, string $alias = null): Builder


whereDistance(string $column, Geometry $geometry, string $operator, int|float $distance, string $boolean = 'and', bool $sphere = false): Builder

orWhereDistance(string $column, Geometry $geometry, string $operator, int|float $distance): Builder


whereDistanceSphere(string $column, Geometry $geometry, string $operator, int|float $distance, string $boolean = 'and'): Builder

orWhereDistanceSphere(string $column, Geometry $geometry, string $operator, int|float $distance): Builder


whereEquals(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder

whereNotEquals(string $column, Geometry $geometry): Builder

orWhereEquals(string $column, Geometry $geometry): Builder

orWhereNotEquals(string $column, Geometry $geometry): Builder


whereContains(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder

whereNotContains(string $column, Geometry $geometry): Builder

orWhereContains(string $column, Geometry $geometry): Builder

orWhereNotContains(string $column, Geometry $geometry): Builder


whereIntersects(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder

whereNotIntersects(string $column, Geometry $geometry): Builder

orWhereIntersects(string $column, Geometry $geometry): Builder

orWhereNotIntersects(string $column, Geometry $geometry): Builder


whereTouches(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder

whereNotTouches(string $column, Geometry $geometry): Builder

orWhereTouches(string $column, Geometry $geometry): Builder

orWhereNotTouches(string $column, Geometry $geometry): Builder


whereOverlaps(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder

whereNotOverlaps(string $column, Geometry $geometry): Builder

orWhereOverlaps(string $column, Geometry $geometry): Builder

orWhereNotOverlaps(string $column, Geometry $geometry): Builder
```

## Geometry Raw

A complete list of all available call functions can be found in the [official documentation](https://postgis.net/docs/reference.html)

Example of using the [ST_Buffer](https://postgis.net/docs/ST_Buffer.html) function

```php
use ArtisanWebLab\GeoJson\Geometry\LineString;
use Illuminate\Support\Facades\DB;

$lineString = new LineString([
    // array of points ...
]);

$geometry = "ST_GeomFromGeoJson('".$lineString."')";

$buffer = "ST_Buffer(".$geometry.", 0.01, 'endcap=round join=round')";

$result = DB::geometryRaw($buffer);
```

```json
{
    "type":"Polygon",
    "coordinates":[
        [
            [30.544479348,50.431502901],
            [30.543614003,50.432667183],
            //..
            [30.544709586,50.431133973],
            [30.544479348,50.431502901]
        ], [
            [30.550099148,50.428327446],
            [30.549938932,50.428458932],
            [30.5500941,50.42832021],
            [30.550099148,50.428327446]
        ]
    ]
}
```
