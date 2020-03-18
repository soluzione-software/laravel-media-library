<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Traits;


use Illuminate\Support\Arr;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\MediaCollection\MediaCollection;
use Spatie\MediaLibrary\Models\Media;

trait HasMedia
{
    use HasMediaTrait;

    /**
     * @return array
     */
    public static function getMediaMediaLibraryValidation()
    {
        return [
            'default' => []
        ];
    }

    /**
     * @return array
     */
    public static function getMediaMediaLibraryCollections()
    {
        return [
            'default'
        ];
    }

    /**
     * @return array
     */
    public static function getMediaMediaLibraryConversions()
    {
        return [];
    }

    /**
     * @param string $collection
     * @return array
     */
    protected static function mediaMediaLibraryConversions(string $collection)
    {
        return Arr::wrap(Arr::get(static::getMediaMediaLibraryConversions(), $collection, []));
    }

    public function registerMediaCollections()
    {
        foreach(static::getMediaMediaLibraryCollections() as $collectionName => $collectionProperties){
            $mediaCollection = $this->addMediaCollection($collectionName);

            if (Arr::get($collectionProperties, 'single_file', false)){
                $mediaCollection = $mediaCollection->singleFile();
            }

            $keepOriginalImageFormat = (bool)Arr::get($collectionProperties, 'keep_original_image_format', false);

            $this->registerConversions($mediaCollection, $keepOriginalImageFormat);
        }
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

                    if ($manipulation === 'resize'){
                        if ($width){
                            $conversion->width($width);
                        }
                        if ($height){
                            $conversion->height($height);
                        }
                    }
                    elseif ($manipulation === 'fit'){
                        $fitMethod = Arr::get($conversionProperties, 'fit_method');
                        $conversion->fit($fitMethod, $width, $height);
                    }

                    if ($keepOriginalImageFormat){
                        $conversion->keepOriginalImageFormat();
                    }
                }
            });
    }
}
