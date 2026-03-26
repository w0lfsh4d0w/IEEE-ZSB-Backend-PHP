<?php
namespace core ; 
class App
{
    // متغير ثابت لتخزين الحاوية
    protected static $container;

    // دالة لوضع الحاوية داخل الكلاس
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    // دالة للحصول على الحاوية من أي مكان
    public static function container()
    {
        return static::$container;
    }
    public static function bind($key, $resolver)
    {
        static::container()->bind($key, $resolver);
    }

    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}