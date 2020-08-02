<?php

namespace App\Observers;

use App\Map;
use Carbon\Carbon;

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

        $map->questions()->each(function ($instance) use ($map) {
            $instance->delete();
        });

        $map->template()->each(function ($instance) {
            $instance->delete();
        });
    }

    /**
     * Handle the map "restoring" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function restoring(Map $map)
    {
        $max_seconds_between_timestamps = 5;

        $max_restore_time = $map->deleted_at->addSeconds($max_seconds_between_timestamps)->toDateTimeString();

        $template = $map->template()->withTrashed()->orderByDesc('deleted_at')->where('deleted_at', '<=', $max_restore_time);

        $questions = $map->questions()->withTrashed()->orderByDesc('deleted_at')->where('deleted_at', '<=', $max_restore_time);

        // Only restore ONE and the NEWEST template that is within x seconds of the map->deleted_at timestamp!
        if($template->count() > 0) {
            $template->first()
                ->restore();
        }

        // Only restore questions that are within x seconds of the map->deleted_at timestamp!
        if($questions->count() > 0) {
            $questions->each(function ($instance) {
                $instance->restore();
            });
        }
    }

    /**
     * Handle the map "restored" event.
     *
     * @param  \App\Map  $map
     * @return void
     */
    public function restored(Map $map)
    {
//        $map->questions()->withTrashed()->each(function ($instance) {
//            $instance->restore();
//        });
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
