<?php

namespace App\Observers;

use App\Map;

class MapObserver
{
    /**
     * Handle the map "created" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function created(Map $map)
    {
        //
    }

    /**
     * Handle the map "updated" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function updated(Map $map)
    {
        //
    }

    /**
     * Handle the map "deleted" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function deleted(Map $map)
    {
        if ($map->isForceDeleting()) {
            return $this->forceDeleted($map);
        }

        $map->questions()->each(function ($instance) {
            $instance->delete();
        });

        $map->image()->each(function ($instance) {
            $instance->delete();
        });
    }

    /**
     * Handle the map "restored" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function restored(Map $map)
    {
        //
    }

    /**
     * Handle the map "force deleted" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function forceDeleted(Map $map)
    {
        // Seems like this method doesn't get called by $item->forceDelete() in controllers. So the observer method above has a if statement...
        dd('Force deleting in observer dd');
    }
}
