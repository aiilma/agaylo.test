<?php

namespace App\Services\Attachment;

use Illuminate\Http\Request;

class Attachment
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function saveToDisk()
    {
        if ($this->exists()) {
            $fileStoragePath = $this->request->attachment->store('support/attachments');
            $fileFullPath = storage_path("app/$fileStoragePath");
            return basename($fileFullPath);
        }

        return null;
    }

    protected function exists()
    {
        return $this->request->hasFile('attachment');
    }
}
