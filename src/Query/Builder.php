<?php

namespace ArtisanWebLab\Spatial\Query;

use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\GeoJson\Geometry\Point;
use Illuminate\Database\Query\Builder as IlluminateBuilder;
use Illuminate\Database\Query\Expression;

class Builder extends IlluminateBuilder
{
    /**
     * @param         $column
     * @param  Point  $point
     * @param  null   $alias
     *
     * @return $this
     */
    public function withDistance($column, Point $point, $alias = null): Builder
    {
        $grammar = $this->getGrammar()->withDistance($this, compact('column', 'point', 'alias'));

        $this->addSelect(new Expression($grammar));

        return $this;
    }

    /**
     * @param         $column
     * @param  Point  $point
     * @param  null   $alias
     *
     * @return $this
     */
    public function withSphereDistance($column, Point $point, $alias = null): Builder
    {
        $grammar = $this->getGrammar()->withDistanceSphere($this, compact('column', 'point', 'alias'));

        $this->addSelect(new Expression($grammar));

        return $this;
    }

    /**
     * @param          $column
     * @param  Point   $point
     * @param          $operator
     * @param          $distance
     * @param  string  $boolean
     *
     * @return $this
     */
    public function whereDistance($column, Point $point, $operator, $distance, $boolean = 'and'): Builder
    {
        $type = 'Distance';

        $this->wheres[] = compact('type', 'column', 'point', 'operator', 'distance', 'boolean');

        return $this;
    }

    /**
     * @param          $column
     * @param  Point   $point
     * @param          $operator
     * @param          $distance
     * @param  string  $boolean
     *
     * @return $this
     */
    public function whereDistanceSphere($column, Point $point, $operator, $distance, $boolean = 'and'): Builder
    {
        $type = 'DistanceSphere';

        $this->wheres[] = compact('type', 'column', 'point', 'operator', 'distance', 'boolean');

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  false     $not
     *
     * @return $this
     */
    public function whereEquals($column, Geometry $geometry, $boolean = 'and', $not = false): Builder
    {
        $type = $not ? 'NotEquals' : 'Equals';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  false     $not
     *
     * @return $this
     */
    public function whereContains($column, Geometry $geometry, $boolean = 'and', $not = false): Builder
    {
        $type = $not ? 'NotContains' : 'Contains';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  false     $not
     *
     * @return $this
     */
    public function whereIntersects($column, Geometry $geometry, $boolean = 'and', $not = false): Builder
    {
        $type = $not ? 'NotIntersects' : 'Intersects';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  false     $not
     *
     * @return $this
     */
    public function whereTouches($column, Geometry $geometry, $boolean = 'and', $not = false): Builder
    {
        $type = $not ? 'NotTouches' : 'Touches';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     * @param  string    $boolean
     * @param  false     $not
     *
     * @return $this
     */
    public function whereOverlaps($column, Geometry $geometry, $boolean = 'and', $not = false): Builder
    {
        $type = $not ? 'NotOverlaps' : 'Overlaps';

        $this->wheres[] = compact('type', 'column', 'geometry', 'boolean');

        return $this;
    }
}