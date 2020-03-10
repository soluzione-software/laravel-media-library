<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Models;


use Spatie\MediaLibrary\Models\Media as SpatieMedia;

/**
 * @property string $collection_name
 */
class Media extends SpatieMedia
{
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'url' => $this->getUrl(),
            'thumbnail' => $this->getUrl(),
        ]);
    }
}
