<?php

namespace App\Http\Controllers\DefaultLaravel;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Delete all other files from the upload table when it should be a MorphOne relation
    // Should be called when saving to database.
    protected function deleteAllOtherUploads($instance)
    {
        $uploads = \App\Upload::where([
            'uploadable_id' => $instance->id,
            'uploadable_type' => get_class($instance),
        ])->get();

        foreach ($uploads as $upload) {
            $upload->delete();
        }
    }
}
