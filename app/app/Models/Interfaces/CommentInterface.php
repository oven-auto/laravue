<?php

namespace App\Models\Interfaces;

Interface CommentInterface
{
    public function addComment(String $message);
    public function selfRussianName();
    public function changesList(Array $arr);
}
