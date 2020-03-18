<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Contracts;


use Spatie\MediaLibrary\FileAdder\FileAdder;

interface HasMedia extends \Spatie\MediaLibrary\HasMedia\HasMedia
{
    /**
     * @return array
     */
    public static function getMediaMediaLibraryValidation();

    /**
     * @return array
     */
    public static function getMediaMediaLibraryCollections();

    /**
     * @return array
     */
    public static function getMediaMediaLibraryConversions();

    /**
     * Add a file from a request.
     *
     * @param string $key
     *
     * @return FileAdder
     */
    public function addMediaFromRequest(string $key);
}
