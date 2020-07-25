<?php

if (! function_exists('save_upload_to_database_HELPER')) {
    function save_upload_to_database_HELPER($file, $instance)
    {

        return $instance->create([
            'file_name' => \Str::random(75) . '.' . $file->getClientOriginalExtension(),
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
