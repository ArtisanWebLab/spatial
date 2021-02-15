<?php

namespace ArtisanWebLab\Spatial\Eloquent;

use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\GeoJson\Geometry\Point;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Expression;

class Builder extends EloquentBuilder
{
    /**
     * @inheritDoc
     */
    public function get($columns = ['*'])
    {
        $model = $this->getModel();

        if (method_exists($model, 'getSpatial')) {
            $spatialFields = $model->getSpatial();

            if (!empty($spatialFields)) {
                if (is_null($this->query->columns)) {
                    $this->query->select([$this->query->from.'.*']);
                }

                foreach ($spatialFields as $field) {
                    // MySQL: We cannot convert directly to JSON because an out of range error occurs
                    // $this->query->addSelect(new Expression("ST_AsGeoJSON(ST_GeomFromText(ST_AsText({$field}))) as {$field}"));

                    // PostgreSQL
                    $this->query->addSelect(new Expression("ST_AsGeoJSON({$field}) as {$field}"));
                }
            }
        }

        return parent::get($columns);
    }

    /**
     * @param         $column
     * @param  Point  $point
     * @param  null   $alias
     * @param  false  $sphere
     *
     * @return $this
     */
    public function withDistance($column, Point $point, $alias = null, $sphere = false): Builder
    {
        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from.'.*']);
        }

        if ($sphere) {
            $this->getQuery()->withSphereDistance($column, $point, $alias);
        } else {
            $this->getQuery()->withDistance($column, $point, $alias);
        }

        return $this;
    }

    /**
     * @param         $column
     * @param  Point  $point
     * @param  null   $alias
     *
     * @return $this
     */
    public function withDistanceSphere($column, Point $point, $alias = null): Builder
    {
        return $this->withDistance($column, $point, $alias, true);
    }

    /**
     * @param          $column
     * @param  Point   $point
     * @param          $operator
     * @param          $distance
     * @param  string  $boolean
     * @param  false   $sphere
     *
     * @return $this
     */
    public function whereDistance(
        $column,
        Point $point,
        $operator,
        $distance,
        $boolean = 'and',
        $sphere = false
    ): Builder {
        if ($sphere) {
            $this->getQuery()->whereDistanceSphere($column, $point, $operator, $distance, $boolean);
        } else {
            $this->getQuery()->whereDistance($column, $point, $operator, $distance, $boolean);
        }

        return $this;
    }

    /**
     * @param         $column
     * @param  Point  $point
     * @param         $operator
     * @param         $distance
     *
     * @return $this
     */
    public function orWhereDistance($column, Point $point, $operator, $distance): Builder
    {
        return $this->whereDistance($column, $point, $operator, $distance, 'or');
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
        $this->whereDistance($column, $point, $operator, $distance, $boolean, true);

        return $this;
    }

    /**
     * @param         $column
     * @param  Point  $point
     * @param         $operator
     * @param         $distance
     *
     * @return $this
     */
    public function orWhereDistanceSphere($column, Point $point, $operator, $distance): Builder
    {
        return $this->whereDistanceSphere($column, $point, $operator, $distance, 'or');
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
        $this->getQuery()->whereEquals($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotEquals($column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'and', true);
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereEquals($column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'or');
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotEquals($column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'or', true);
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
        $this->getQuery()->whereContains($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotContains($column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'and', true);
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereContains($column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'or');
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotContains($column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'or', true);
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
        $this->getQuery()->whereIntersects($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotIntersects($column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'and', true);
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereIntersects($column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'or');
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotIntersects($column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'or', true);
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
        $this->getQuery()->whereTouches($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotTouches($column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'and', true);
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereTouches($column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'or');
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotTouches($column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'or', true);
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
        $this->getQuery()->whereOverlaps($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotOverlaps($column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'and', true);
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereOverlaps($column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'or');
    }

    /**
     * @param            $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotOverlaps($column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'or', true);
    }
}
