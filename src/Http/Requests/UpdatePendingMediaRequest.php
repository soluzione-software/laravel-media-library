<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Http\Requests;

class UpdatePendingMediaRequest extends PendingMediaRequest
{
    protected function getMediaRules()
    {
        return $this->updatePendingMediaRules($this->getModelClass(), $this->getCollectionName());
    }
}
