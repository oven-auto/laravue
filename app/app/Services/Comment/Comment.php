<?php

namespace App\Services\Comment;

use App\Models\Interfaces\CommentInterface;

Class Comment
{
    public static $comment;
    private static function makeFactory($model)
    {
        $name = (new \ReflectionClass($model))->getShortName();

        $class = self::getNameSpace().'\\'.$name.'Comment';

        if(class_exists($class))
            self::$comment = new $class($model);
    }

    private static function getNameSpace()
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getNamespaceName();
    }

    /*----------------------------------------------------------------------*/

    public static function add(CommentInterface $model, string $action)
    {
        self::makeFactory($model);
        if(method_exists(self::$comment, $action))
        return $model->writeComment(self::$comment->$action($model));
    }

    public static function text(CommentInterface $model, string $message)
    {
        self::makeFactory($model);
        return $model->writeComment(self::$comment->text($message));
    }

    public static function getComment()
    {
        return self::$comment->getData();
    }
}
