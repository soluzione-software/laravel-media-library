<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Models;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @property string $collection_name
 * @property string $url
 * @property-read Collection conversions
 */
class Media extends SpatieMedia
{
    protected $hidden = ['model_type', 'model_id', 'disk', 'size', 'manipulations', 'created_at', 'updated_at'];

    protected $appends = ['url', 'conversions'];

    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    public function getConversionsAttribute()
    {
        return $this
            ->getGeneratedConversions()
            ->filter(function ($value) {
                return $value;
            })
            ->map(function ($value, $key) {
                return $this->getUrl($key);
            });
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'thumbnail' => $this->getUrl(),
        ]);
    }
}
