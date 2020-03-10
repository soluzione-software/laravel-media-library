<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use SoluzioneSoftware\LaravelMediaLibrary\Models\Media;

trait StoresMedia
{
    /**
     * @see ValidatesMedia::storeMediaRules
     * @param FormRequest $request
     * @param Model|HasMedia $model
     * @return boolean
     */
    private function storeMedia(FormRequest $request, Model $model)
    {
        $validated = $request->validated();

        $collections = $model::getMediaMediaLibraryCollections();

        return rescue(function () use ($collections, $validated, $model) {
            foreach ($collections as $collectionName => $collectionProperties) {
                // store new
                $mediaItemsToStore = Arr::get($validated, "media.store.$collectionName", []);
                for ($i = 0; $i < count($mediaItemsToStore); $i++){
                    $model
                        ->addMediaFromRequest("media.store.$collectionName.$i.file")
                        // todo: add custom properties
                        ->toMediaCollection($collectionName);
                }
            }

            return true;
        }, false);
    }

    /**
     * @param FormRequest $request
     * @param Model|HasMedia $model
     * @return boolean
     * @see ValidatesMedia::updateMediaRules
     */
    private function updateMedia(FormRequest $request, Model $model)
    {
        $validated = $request->validated();

        $collections = $model::getMediaMediaLibraryCollections();

        return rescue(function () use ($collections, $validated, $model) {
            foreach ($collections as $collectionName => $collectionProperties) {
                // store new
                $mediaItemsToStore = Arr::get($validated, "media.store.$collectionName", []);
                for ($i = 0; $i < count($mediaItemsToStore); $i++){
                    $model
                        ->addMediaFromRequest("media.store.$collectionName.$i.file")
                        // todo: add custom properties
                        ->toMediaCollection($collectionName);
                }

                // update
                $mediaItemsToUpdate = Arr::get($validated, "media.update", []);
                for ($i = 0; $i < count($mediaItemsToUpdate); $i++){
                    /** @var Media|null $media */
                    $media = $model->media()->where('id', Arr::get($mediaItemsToUpdate[$i], 'id'))->first();
                    if (is_null($media)){
                        continue;
                    }
                    $collectionName = $media->collection_name;
                    $media->delete();
                    $model
                        ->addMediaFromRequest("media.update.$i.file")
                        // todo: add custom properties
                        ->toMediaCollection($collectionName);
                }

                // delete
                foreach (Arr::get($validated, "media.delete", []) as $id) {
                    $media = $model->media()->where('id', $id)->first();
                    if (is_null($media)){
                        continue;
                    }

                    $media->delete();
                }
            }

            return true;
        }, false);
    }
}
