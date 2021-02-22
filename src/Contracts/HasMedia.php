<?php

namespace SoluzioneSoftware\LaravelMediaLibrary\Contracts;

interface HasMedia extends \Spatie\MediaLibrary\HasMedia
{
    public static function getMediaMediaLibraryValidation(): array;

    public static function getMediaMediaLibraryCollections(): array;

    public static function getMediaMediaLibraryConversions(): array;
}
