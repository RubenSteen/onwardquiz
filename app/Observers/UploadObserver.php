<?php

namespace App\Observers;

use App\Upload;
use Illuminate\Http\Request;

class UploadObserver
{
    /**
     * Handle the upload "creating" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function creating(Upload $upload)
    {
    }

    /**
     * Handle the upload "created" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function created(Upload $upload)
    {
    }

    /**
     * Handle the upload "updated" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function updated(Upload $upload)
    {
    }

    /**
     * Handle the upload "deleted" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function deleted(Upload $upload)
    {
        if ($upload->isForceDeleting()) {
            return $this->forceDeleted($upload);
        } else {
            // Soft deleting
        }
    }

    /**
     * Handle the upload "restored" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function restored(Upload $upload)
    {
    }

    /**
     * Handle the upload "force deleted" event.
     *
     * @param  \App\Upload  $upload
     * @return void
     */
    public function forceDeleted(Upload $upload)
    {
        // Seems like this method doesn't get called by $item->forceDelete() in controllers. So the observer method above has a if statement...

        // Removes the image from the file system if it exists.
        dd('Force deleting in observer dd');
    }
}
