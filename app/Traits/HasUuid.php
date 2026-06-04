<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the HasUuid trait.
     * Automatically generates a UUID for the model's primary key when creating.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Disable auto-incrementing for UUID primary keys.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Set the key type to string for UUID primary keys.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
