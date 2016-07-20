<?php

namespace ImguBox\Entities;

use ImguBox\User;

class Favorites
{
    protected $favorites;

    protected $user;

    public function __construct(array $favorites, User $user)
    {
        $this->favorites = collect($favorites);
        $this->user = $user;

        $this->removeProcessedFavorites();
    }

    /**
     * Return Favorites Collection.
     *
     * @return Collection
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    public function removeProcessedFavorites()
    {
        $processedImages = $this->user->logs->lists('imgur_id');

        $freshFavorites = $this->favorites->filter(function ($item) use ($processedImages) {
            return !in_array($item->getId(), $processedImages->toArray());
        });

        $this->favorites = $freshFavorites;
    }
}
