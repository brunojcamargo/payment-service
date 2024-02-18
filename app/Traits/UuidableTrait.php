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
        $strHelper = app(Str::class);
        static::creating(function ($model) use($strHelper) {
            $model->id = substr(str_replace('-', '', $strHelper->uuid()), 0, 6);
        });
    }
}