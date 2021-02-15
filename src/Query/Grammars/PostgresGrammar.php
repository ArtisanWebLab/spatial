<?php

namespace ArtisanWebLab\Spatial\Query\Grammars;

use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\Spatial\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\PostgresGrammar as IlluminatePostgresGrammar;

class PostgresGrammar extends IlluminatePostgresGrammar
{
    /**
     * @param        $value
     * @param  bool  $prefixAlias
     *
     * @return string
     */
    public function wrapST($value, bool $prefixAlias = false): string
    {
        return parent::wrap($value, $prefixAlias);
    }

    /**
     * @param  Geometry  $geometry
     *
     * @return string
     */
    public function STGeomFromObject(Geometry $geometry): string
    {
        $jsonString = json_encode($geometry->jsonSerialize());

        return "ST_GeomFromGeoJSON('{$jsonString}')";
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return string
     */
    public function STDistance(string $column, Geometry $geometry): string
    {
        return "ST_Distance({$column}, {$this->STGeomFromObject($geometry)}, false)";
    }

    /**
     * @param  string    $column
     * @param  Geometry  $geometry
     *
     * @return string
     */
    public function STDistanceSphere(string $column, Geometry $geometry): string
    {
        return "ST_Distance({$column}, {$this->STGeomFromObject($geometry)}, true)";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function withDistance(QueryBuilder $query, array $where): string
    {
        $alias = !empty($where['alias']) ? $where['alias'] : "{$where['column']}_distance";

        return "{$this->STDistance($where['column'], $where['geometry'])} as {$alias}";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function withDistanceSphere(QueryBuilder $query, array $where): string
    {
        $alias = !empty($where['alias']) ? $where['alias'] : "{$where['column']}_distance_sphere";

        return "{$this->STDistanceSphere($where['column'], $where['geometry'])} as {$alias}";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereDistance(QueryBuilder $query, array $where): string
    {
        return "{$this->STDistance($where['column'], $where['geometry'])} {$where['operator']} {$where['distance']}";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereDistanceSphere(QueryBuilder $query, array $where): string
    {
        return "{$this->STDistanceSphere($where['column'], $where['geometry'])} {$where['operator']} {$where['distance']}";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereEquals(QueryBuilder $query, array $where): string
    {
        return "ST_Equals({$this->wrapST($where['column'])}, {$this->STGeomFromObject($where['geometry'])})";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereNotEquals(QueryBuilder $query, array $where): string
    {
        return "not ".$this->whereEquals($query, $where);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereContains(QueryBuilder $query, array $where): string
    {
        return "ST_Contains({$this->wrapST($where['column'])}, {$this->STGeomFromObject($where['geometry'])})";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereNotContains(QueryBuilder $query, array $where): string
    {
        return "not ".$this->whereContains($query, $where);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereIntersects(QueryBuilder $query, array $where): string
    {
        return "ST_Intersects({$this->wrapST($where['column'])}, {$this->STGeomFromObject($where['geometry'])})";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereNotIntersects(QueryBuilder $query, array $where): string
    {
        return "not ".$this->whereIntersects($query, $where);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereTouches(QueryBuilder $query, array $where): string
    {
        return "ST_Touches({$this->wrapST($where['column'])}, {$this->STGeomFromObject($where['geometry'])})";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereNotTouches(QueryBuilder $query, array $where): string
    {
        return "not ".$this->whereTouches($query, $where);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereOverlaps(QueryBuilder $query, array $where): string
    {
        return "ST_Overlaps({$this->wrapST($where['column'])}, {$this->STGeomFromObject($where['geometry'])})";
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array         $where
     *
     * @return string
     */
    public function whereNotOverlaps(QueryBuilder $query, array $where): string
    {
        return "not ".$this->whereOverlaps($query, $where);
    }
}
