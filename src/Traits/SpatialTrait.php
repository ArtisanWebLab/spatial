<?php

namespace ArtisanWebLab\Spatial\Traits;

use ArtisanWebLab\GeoJson\GeoJson;
use ArtisanWebLab\GeoJson\Geometry\Geometry;
use ArtisanWebLab\Spatial\Eloquent\Builder as EloquentBuilder;
use ArtisanWebLab\Spatial\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;

/**
 * Trait SpatialTrait
 *
 * @package ArtisanWebLab\Spatial\Traits
 * @property array $spatial
 */
trait SpatialTrait
{
    public static function bootSpatialTrait(): void
    {
        static::retrieved(function (self $model) {
            $spatialFields = $model->getSpatialAttributes();

            array_map(function ($field, $value) use ($model) {
                $geometry = GeoJson::jsonUnserialize($value);

                $model->setAttribute($field, $geometry);
            }, array_keys($spatialFields), $spatialFields);
        });

        static::saving(function (self $model) {
            $spatialFields = $model->getSpatialAttributes();

            array_map(function ($field, $value) use ($model) {
                if (!empty($value)) {
                    if (!$value instanceof Geometry) {
                        $value = GeoJson::jsonUnserialize($value);
                    }

                    $expression = new Expression(sprintf("ST_GeomFromGeoJSON('%s')", json_encode($value)));
                } else {
                    $expression = null;
                }

                $model->setAttribute($field, $expression);
            }, array_keys($spatialFields), $spatialFields);
        });
    }

    protected function initializeSpatialTrait(): void
    {
        //
    }

    /**
     * @return array
     */
    public function getSpatial(): array
    {
        return $this->spatial ?? [];
    }

    /**
     * @return array
     */
    public function getSpatialAttributes(): array
    {
        $attributes = $this->getAttributes();

        $spatialFields = array_flip($this->getSpatial());

        return array_diff_key($attributes, array_diff_key($attributes, $spatialFields));
    }

    /**
     * @inheritDoc
     */
    public function newEloquentBuilder($query): EloquentBuilder
    {
        return new EloquentBuilder($query);
    }

    /**
     * @inheritDoc
     */
    protected function newBaseQueryBuilder(): QueryBuilder
    {
        $connection = $this->getConnection();

        return new QueryBuilder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());
    }
}
