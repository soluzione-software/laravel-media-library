<?php


namespace SoluzioneSoftware\LaravelMediaLibrary\Models;


use Illuminate\Database\Eloquent\Model;
use SoluzioneSoftware\LaravelMediaLibrary\Contracts\HasMedia as HasMediaContract;
use SoluzioneSoftware\LaravelMediaLibrary\Traits\HasMedia;

/**
 * @property int id
 */
class PendingMedia extends Model implements HasMediaContract
{
    use HasMedia;
}
