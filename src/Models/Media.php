<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Models;


use Spatie\MediaLibrary\Models\Media as SpatieMedia;

/**
 * @property string $collection_name
 */
class Media extends SpatieMedia
{
    protected $hidden = ['model_type', 'model_id', 'disk', 'size', 'manipulations', 'created_at', 'updated_at'];

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'url' => $this->getUrl(),
            'thumbnail' => $this->getUrl(),
        ]);
    }
}
