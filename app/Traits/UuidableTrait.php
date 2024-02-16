<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidableTrait
{
    /**
     * Boot the UUID trait for a model.
     *
     * @return void
     */
    protected static function bootUuidableTrait()
    {
        static::creating(function ($model) {
            $model->id = substr(str_replace('-', '', Str::uuid()), 0, 6);
        });
    }
}