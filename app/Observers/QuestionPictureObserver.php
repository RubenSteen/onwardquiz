<?php

namespace App\Observers;

use App\QuestionPicture;

class QuestionPictureObserver
{
    /**
     * Handle the picture "created" event.
     *
     * @param  \App\QuestionPicture  $picture
     * @return void
     */
    public function created(QuestionPicture $picture)
    {
        //
    }

    /**
     * Handle the picture "updated" event.
     *
     * @param  \App\QuestionPicture  $picture
     * @return void
     */
    public function updated(QuestionPicture $picture)
    {
        //
    }

    /**
     * Handle the picture "deleted" event.
     *
     * @param  \App\QuestionPicture  $picture
     * @return void
     */
    public function deleted(QuestionPicture $picture)
    {
        if ($picture->isForceDeleting()) {
            return $this->forceDeleted($picture);
        }

        $picture->image()->each(function ($instance) {
            $instance->delete();
        });
    }

    /**
     * Handle the picture "restored" event.
     *
     * @param  \App\QuestionPicture  $picture
     * @return void
     */
    public function restored(QuestionPicture $picture)
    {
        $picture->image()->onlyTrashed()->each(function ($instance) {
            $instance->restore();
        });
    }

    /**
     * Handle the picture "force deleted" event.
     *
     * @param  \App\QuestionPicture  $picture
     * @return void
     */
    public function forceDeleted(QuestionPicture $picture)
    {
        // Seems like this method doesn't get called by $item->forceDelete() in controllers. So the observer method above has a if statement...
        dd('Force deleting in observer dd');
    }
}
