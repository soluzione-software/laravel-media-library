<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Models;


use Spatie\MediaLibrary\Models\Media as SpatieMedia;

/**
 * @property string $collection_name
 * @property string $url
 */
class Media extends SpatieMedia
{
    protected $hidden = ['model_type', 'model_id', 'disk', 'size', 'manipulations', 'created_at', 'updated_at'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'thumbnail' => $this->getUrl(),
        ]);
    }
}
