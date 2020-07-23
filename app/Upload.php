<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        Upload::observe(Observers\UploadObserver::class);
    }

    protected $fillable = [
        'name', 'extension', 'size', 'mime_type', 'file_name'
    ];

    public function uploadable()
    {
        return $this->morphTo();
    }

    // Outputs the storage folder
    // @String : "app/public/uploads/2020/03/11/"
    public function getFolder()
    {
        return storage_path("app/public/uploads/{$this->getCreatedDateFolderStructure()}/");
    }

    // Outputs the storage folder with file
    // @String : "app/public/uploads/2020/03/11/file.jpg"
    public function getFolderWithFile()
    {
        return $this->getFolder() . $this->file_name;
    }

    // Outputs the folder that can be used in a URL
    // @String : "storage/uploads/2020/03/11/"
    public function getPublicURLFolder()
    {
        return "storage/uploads/{$this->getCreatedDateFolderStructure()}/";
    }

    // Outputs the folder with file that can be used in a URL
    // @String : "storage/uploads/2020/03/11/file.jpg"
    public function getPublicURLFolderWithFile()
    {
        return $this->getPublicURLFolder() . $this->file_name;
    }
    
    // Return in URL format.
    // @String : "http://domain.com/storage/uploads/2020/03/11/"
    public function getAssetFolder()
    {
        return asset($this->getPublicURLFolder()) . "/";
    }

    // Return in URL format.
    // @String : "http://domain.com/storage/uploads/2020/03/11/file.jpg"
    public function getAssetFolderWithFile()
    {
        return $this->getAssetFolder() . $this->file_name;
    }

    // Returns the structure that uploads gets saved to
    // @String : "2020/03/11"
    public function getCreatedDateFolderStructure()
    {
        return "{$this->created_at->isoFormat('Y')}/{$this->created_at->isoFormat('MM')}/{$this->created_at->isoFormat('DD')}";
    }

}
