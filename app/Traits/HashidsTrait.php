<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HashidsTrait
{
    /**
     * Get the route key for the model.
     */
    public function getRouteKey(): string
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        
        if (empty($decoded)) {
            abort(404);
        }

        return $this->where($field ?? $this->getRouteKeyName(), $decoded[0])->firstOrFail();
    }

    /**
     * Get the hashid of the model.
     */
    public function getHashidAttribute(): string
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Decode hashid to id.
     */
    public static function decodeHashid(string $hashid): ?int
    {
        $decoded = Hashids::decode($hashid);
        return $decoded[0] ?? null;
    }

    /**
     * Encode id to hashid.
     */
    public static function encodeId(int $id): string
    {
        return Hashids::encode($id);
    }
}
