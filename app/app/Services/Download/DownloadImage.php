<?php

namespace App\Services\Download;
use Storage;
use Illuminate\Http\UploadedFile;

Class DownloadImage
{
    private $file;
    private $prefix = 'ovenauto';
    private $catalog;
    private $pathName;
    private $root = '/public';

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;
        return $this;
    }

    public function setPathName($str)
    {
        $this->pathName = $str;
        return $this;
    }

    private function storagePath($root = '')
    {
        $path = $root;
        $path .= $this->catalog ? '/'.$this->catalog : '';
        $path .= $this->pathName ? '/'.$this->pathName : '';
        return $path;
    }

    public function save()
    {
        if($this->file instanceof UploadedFile)
            return $this->load();
        throw new Exception('Не выбран фаил');
    }

    private function load()
    {
        $fileName = $this->prefix.'_'.date('dmyhms').'.'.$this->file->getClientOriginalExtension();

        $path = $this->storagePath($this->root);

        $finalName = $this->storagePath().'/'.$this->file->move(Storage::path($path), $fileName)->getFilename();

        return $finalName;
    }
}
