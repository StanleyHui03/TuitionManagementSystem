<?php

namespace App\Models\Concerns;

trait HasPrefixedId
{
    protected static function bootHasPrefixedId()
    {
        static::creating(function ($model) {
            $keyName = $model->getKeyName();

            // Skip if already set
            if ($model->{$keyName}) return;

            $prefix = static::ID_PREFIX ?? '';
            $pad    = static::ID_PAD_LENGTH ?? 4;

            // Find the latest record with this prefix
            $latest = static::where($keyName, 'like', $prefix.'%')
                ->orderBy($keyName, 'desc')
                ->first();

            $next = $latest
                ? intval(substr($latest->{$keyName}, strlen($prefix))) + 1
                : 1;

            $model->{$keyName} = $prefix . str_pad($next, $pad, '0', STR_PAD_LEFT);
        });
    }
}
