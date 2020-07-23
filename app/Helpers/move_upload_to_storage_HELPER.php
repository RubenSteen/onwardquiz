<?php

if (! function_exists('move_upload_to_storage_HELPER')) {
    function move_upload_to_storage_HELPER($file, $upload) {
        $file->move($upload->getFolder(), $upload->file_name);
    }
} 