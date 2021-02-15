## Laravel Spatial

```shell
composer require artisanweblab/spatial
```

## Integration with your project

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

## Input/Output

```php
<?php

use App\Models\User;
use ArtisanWebLab\GeoJson\Geometry\MultiPoint;
use ArtisanWebLab\GeoJson\Geometry\Point;

/** @var User $user */
$user = User::query()
    ->first();

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
