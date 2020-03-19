<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Traits;


use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use SoluzioneSoftware\LaravelMediaLibrary\Contracts\HasMedia as HasMediaContract;
use SoluzioneSoftware\LaravelMediaLibrary\Models\Media;
use SoluzioneSoftware\LaravelMediaLibrary\Models\PendingMedia;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\RequestDoesNotHaveFile;

trait StoresMedia
{
    /**
     * @param FormRequest $request
     * @param HasMediaContract $model
     * @return boolean
     * @see ValidatesMedia::storeMediaRules
     */
    private function storeMedia(FormRequest $request, HasMediaContract $model)
    {
        $validated = $request->validated();

        $collections = $model::getMediaMediaLibraryCollections();

        return rescue(function () use ($collections, $validated, $model) {
            foreach ($collections as $collectionName => $collectionProperties) {
                // store new
                $mediaItemsToStore = Arr::get($validated, "media.store.$collectionName", []);
                for ($i = 0; $i < count($mediaItemsToStore); $i++){
                    $this->addMedia($model, "media.store.$collectionName.$i.", $validated, $collectionName);
                }
            }

            return true;
        }, false);
    }

    /**
     * @param FormRequest $request
     * @param HasMediaContract $model
     * @return boolean
     * @see ValidatesMedia::updateMediaRules
     */
    private function updateMedia(FormRequest $request, HasMediaContract $model)
    {
        $validated = $request->validated();

        $collections = $model::getMediaMediaLibraryCollections();

        return rescue(function () use ($collections, $validated, $model) {
            foreach ($collections as $collectionName => $collectionProperties) {
                // store new
                $mediaItemsToStore = Arr::get($validated, "media.store.$collectionName", []);
                for ($i = 0; $i < count($mediaItemsToStore); $i++){
                    $this->addMedia($model, "media.store.$collectionName.$i.", $validated, $collectionName);
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
                    $this->addMedia($model, "media.update.$i.", $validated, $collectionName);
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

    /**
     * @param PendingMedia $model
     * @return \Spatie\MediaLibrary\Models\Media
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @see ValidatesMedia::storePendingMediaRules()
     */
    private function storePendingMedia(PendingMedia $model)
    {
        return $this->addMedia($model, 'media.');
    }

    /**
     * @param PendingMedia $model
     * @return \Spatie\MediaLibrary\Models\Media
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @see ValidatesMedia::updatePendingMediaRules()
     */
    private function updatePendingMedia(PendingMedia $model)
    {
        $model->media()->delete();

        return $this->addMedia($model, 'media.');
    }

    /**
     * @param HasMediaContract $model
     * @param string|null $inputPrefix
     * @param array|null $validatedInputs
     * @param string|null $collectionName
     * @return Media|null
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws Exception
     */
    private function addMedia(HasMediaContract $model, ?string $inputPrefix = '', ?array $validatedInputs = [], ?string $collectionName = 'default')
    {
        try {
            $media = $model
                ->addMediaFromRequest("{$inputPrefix}file")
                // todo: add custom properties
                ->toMediaCollection($collectionName);
        }
        catch (RequestDoesNotHaveFile $exception){
            /** @var PendingMedia|null $pendingMedia */
            $pendingMedia = PendingMedia::query()->find(Arr::get($validatedInputs, "{$inputPrefix}pending_media_id"));
            if (is_null($pendingMedia)){
                Log::info('PendingMedia not found');
                return null;
            }

            /** @var Media|null $mediaItem */
            $mediaItem = $pendingMedia->media()->first();
            $media = $mediaItem->move($model, $collectionName);

            $pendingMedia->delete();
        }

        return $media;
    }
}
