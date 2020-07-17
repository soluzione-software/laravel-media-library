<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Http\Requests;

class StorePendingMediaRequest extends PendingMediaRequest
{
    protected function getMediaRules()
    {
        return $this->storePendingMediaRules($this->getModelClass(), $this->getCollectionName());
    }
}
