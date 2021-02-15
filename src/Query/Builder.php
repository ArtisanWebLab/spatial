<?php

namespace ArtisanWebLab\Spatial\Query;

use ArtisanWebLab\GeoJson\Geometry\Geometry;
use Illuminate\Database\Query\Builder as IlluminateQueryBuilder;
use Illuminate\Database\Query\Expression;

class Builder extends IlluminateQueryBuilder
{
    /**
     * @param  string       $column
     * @param  Geometry     $geometry
     * @param  string|null  $alias
     *
     * @return $this
     */
    public function withDistance(string $column, Geometry $geometry, string $alias = null): Builder
    {
        $grammar = $this->getGrammar()->withDistance($this, compact('column', 'geometry', 'alias'));

        $this->addSelect(new Expression($grammar));

        return $this;
    }

    /**
     * @param  string       $column
     * @param  Geometry     $geometry
     * @param  string|null  $alias
     *
     * @return $this
     */
    public function withSphereDistance(string $column, Geometry $geometry, string $alias = null): Builder
    {
        $grammar = $this->getGrammar()->withDistanceSphere($this, compact('column', 'geometry', 'alias'));

        $this->addSelect(new Expression($grammar));

        return $this;
    }

    /**
     * @param  string     $column
     * @param  Geometry   $geometry
     * @param  string     $operator
     * @param  int|float  $distance
     * @param  string     $boolean
     *
     * @return $this
     */
    public function whereDistance(string $column, Geometry $geometry, string $operator, int|float $distance, string $boolean = 'and'): Builder
    {
        $type = 'Distance';

        $this->wheres[] = compact('type', 'column', 'geometry', 'operator', 'distance', 'boolean');

        return $this;
    }

    /**
     * @param  string     $column
     * @param  Geometry   $geometry
     * @param  string     $operator
     * @param  int|float  $distance
     * @param  string     $boolean
     *
     * @return $this
     */
    public function whereDistanceSphere(string $column, Geometry $geometry, string $operator, int|float $distance, string $boolean = 'and'): Builder
    {
        $type = 'DistanceSphere';

        $this->wheres[] = compact('type', 'column', 'geometry', 'operator', 'distance', 'boolean');

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  bool      $not
     *
     * @return $this
     */
    public function whereEquals(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder
    {
        $type = $not ? 'NotEquals' : 'Equals';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  bool      $not
     *
     * @return $this
     */
    public function whereContains(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder
    {
        $type = $not ? 'NotContains' : 'Contains';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  bool      $not
     *
     * @return $this
     */
    public function whereIntersects(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder
    {
        $type = $not ? 'NotIntersects' : 'Intersects';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  bool      $not
     *
     * @return $this
     */
    public function whereTouches(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder
    {
        $type = $not ? 'NotTouches' : 'Touches';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  bool      $not
     *
     * @return $this
     */
    public function whereOverlaps(string $column, Geometry $geometry, string $boolean = 'and', bool $not = false): Builder
    {
        $type = $not ? 'NotOverlaps' : 'Overlaps';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }
}
