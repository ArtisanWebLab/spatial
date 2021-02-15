<?php

namespace ArtisanWebLab\Spatial\Eloquent;

use ArtisanWebLab\GeoJson\GeoJson;
use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\GeoJson\Geometry\LineString;
use Illuminate\Database\Eloquent\Builder as IlluminateEloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Builder extends IlluminateEloquentBuilder
{
    /**
     * @inheritDoc
     */
    public function get($columns = ['*']): Collection|array
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
     * @param  string       $column
     * @param  Geometry     $geometry
     * @param  string|null  $alias
     * @param  bool         $sphere
     *
     * @return $this
     */
    public function withDistance(string $column, Geometry $geometry, string $alias = null, bool $sphere = false): Builder
    {
        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from.'.*']);
        }

        if ($sphere) {
            $this->getQuery()->withSphereDistance($column, $geometry, $alias);
        } else {
            $this->getQuery()->withDistance($column, $geometry, $alias);
        }

        return $this;
    }

    /**
     * @param  string       $column
     * @param  Geometry     $geometry
     * @param  string|null  $alias
     *
     * @return $this
     */
    public function withDistanceSphere(string $column, Geometry $geometry, string $alias = null): Builder
    {
        return $this->withDistance($column, $geometry, $alias, true);
    }

    /**
     * @param  string     $column
     * @param  Geometry   $geometry
     * @param  string     $operator
     * @param  int|float  $distance
     * @param  string     $boolean
     * @param  bool       $sphere
     *
     * @return $this
     */
    public function whereDistance(string $column, Geometry $geometry, string $operator, int|float $distance, string $boolean = 'and', bool $sphere = false): Builder
    {
        if ($sphere) {
            $this->getQuery()->whereDistanceSphere($column, $geometry, $operator, $distance, $boolean);
        } else {
            $this->getQuery()->whereDistance($column, $geometry, $operator, $distance, $boolean);
        }

        return $this;
    }

    /**
     * @param  string     $column
     * @param  Geometry   $geometry
     * @param  string     $operator
     * @param  int|float  $distance
     *
     * @return $this
     */
    public function orWhereDistance(string $column, Geometry $geometry, string $operator, int|float $distance): Builder
    {
        return $this->whereDistance($column, $geometry, $operator, $distance, 'or');
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
        $this->whereDistance($column, $geometry, $operator, $distance, $boolean, true);

        return $this;
    }

    /**
     * @param  string     $column
     * @param  Geometry   $geometry
     * @param  string     $operator
     * @param  int|float  $distance
     *
     * @return $this
     */
    public function orWhereDistanceSphere(string $column, Geometry $geometry, string $operator, int|float $distance): Builder
    {
        return $this->whereDistanceSphere($column, $geometry, $operator, $distance, 'or');
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
        $this->getQuery()->whereEquals($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotEquals(string $column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'and', true);
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereEquals(string $column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'or');
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotEquals(string $column, Geometry $geometry): Builder
    {
        return $this->whereEquals($column, $geometry, 'or', true);
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
        $this->getQuery()->whereContains($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotContains(string $column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'and', true);
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereContains(string $column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'or');
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotContains(string $column, Geometry $geometry): Builder
    {
        return $this->whereContains($column, $geometry, 'or', true);
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
        $this->getQuery()->whereIntersects($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotIntersects(string $column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'and', true);
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereIntersects(string $column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'or');
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotIntersects(string $column, Geometry $geometry): Builder
    {
        return $this->whereIntersects($column, $geometry, 'or', true);
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
        $this->getQuery()->whereTouches($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotTouches(string $column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'and', true);
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereTouches(string $column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'or');
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotTouches(string $column, Geometry $geometry): Builder
    {
        return $this->whereTouches($column, $geometry, 'or', true);
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
        $this->getQuery()->whereOverlaps($column, $geometry, $boolean, $not);

        return $this;
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function whereNotOverlaps(string $column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'and', true);
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereOverlaps(string $column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'or');
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return $this
     */
    public function orWhereNotOverlaps(string $column, Geometry $geometry): Builder
    {
        return $this->whereOverlaps($column, $geometry, 'or', true);
    }
}
