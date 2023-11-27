<?php

namespace App\Services\Download;
use Storage;
use Illuminate\Http\UploadedFile;

Class DownloadImage
{
    protected $file;
    protected $prefix = 'ovenauto';
    protected $catalog;
    protected $pathName;
    protected $root = '/public';

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

    public function save($timeSufix = true)
    {
        if($this->file instanceof UploadedFile)
            return $this->load($timeSufix);
        throw new Exception('Не выбран фаил');
    }

    protected function load($timeSufix = true)
    {
        $sufix = '';
        if($timeSufix == true)
            $sufix = date('dmyhms');

        $fileName = $this->prefix.''.$sufix.'_'.$this->file->getClientOriginalName();


        $path = $this->storagePath($this->root);

        $finalName = $this->storagePath().'/'.$this->file->move(Storage::path($path), $fileName)->getFilename();

        return $finalName;
    }
}
