<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Traits;

use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMedia
{
    use \Spatie\MediaLibrary\InteractsWithMedia;

    public static function getMediaMediaLibraryValidation(): array
    {
        return [
            'default' => [],
        ];
    }

    public function registerMediaCollections(): void
    {
        foreach (static::getMediaMediaLibraryCollections() as $collectionName => $collectionProperties) {
            $this->registerCollection($collectionName, $collectionProperties);
        }
    }

    public static function getMediaMediaLibraryCollections(): array
    {
        return [
            'default' => [],
        ];
    }

    protected function registerCollection(string $collectionName, array $collectionProperties)
    {
        $mediaCollection = $this->addMediaCollection($collectionName);

        if (Arr::get($collectionProperties, 'single_file', false)) {
            $mediaCollection = $mediaCollection->singleFile();
        }

        $keepOriginalImageFormat = (bool) Arr::get($collectionProperties, 'keep_original_image_format', false);

        $this->registerConversions($mediaCollection, $keepOriginalImageFormat);
    }

    protected function registerConversions(MediaCollection $mediaCollection, bool $keepOriginalImageFormat)
    {
        $conversions = static::mediaMediaLibraryConversions($mediaCollection->name);

        $mediaCollection
            ->registerMediaConversions(function (Media $media) use ($conversions, $keepOriginalImageFormat) {
                foreach ($conversions as $conversionName => $conversionProperties) {
                    $conversion = $this->addMediaConversion($conversionName);

                    $manipulation = Arr::get($conversionProperties, 'manipulation');
                    $width = Arr::get($conversionProperties, 'width');
                    $height = Arr::get($conversionProperties, 'height');

                    if ($manipulation === 'resize') {
                        if ($width) {
                            $conversion->width($width);
                        }
                        if ($height) {
                            $conversion->height($height);
                        }
                    } elseif ($manipulation === 'fit') {
                        $fitMethod = Arr::get($conversionProperties, 'fit_method');
                        $conversion->fit($fitMethod, $width, $height);
                    }

                    if ($keepOriginalImageFormat) {
                        $conversion->keepOriginalImageFormat();
                    }
                }
            });
    }

    /**
     * @param  string  $collection
     * @return array
     */
    protected static function mediaMediaLibraryConversions(string $collection)
    {
        return Arr::wrap(Arr::get(static::getMediaMediaLibraryConversions(), $collection, []));
    }

    public static function getMediaMediaLibraryConversions(): array
    {
        return [
            'default' => [],
        ];
    }
}
