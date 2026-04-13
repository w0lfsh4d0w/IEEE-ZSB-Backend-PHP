<?php

namespace core;

use SeekableIterator;

class Session
{
    public static function has($key)
    {
        return (bool)  static::get($key);
    }
    public static function  put($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public static function  get($key, $default = null)
    {
        if (isset($_SESSION['_flash'][$key])) {
            return $_SESSION['_flash'][$key];
        }

        return $_SESSION[$key] ?? $default;
    }
    public static function flash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }
    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }
    public static function flush()
    {
        $_SESSION = [];
    }
    public static function destroy()
    {

        // 1. تفريغ المصفوفة من الذاكرة الحالية للسكربت
        // هذا يؤثر فقط على المستخدم الحالي (صاحب الطلب) ولا يمس المستخدمين الآخرين
        static::flush();

        // 2. تدمير الملف الفعلي من الهارد ديسك الخاص بالسيرفر
        // لحماية النظام من أي هاكر قد يكون سرق مُعرف الجلسة (Session ID)
        session_destroy();

        // 3. حذف مفتاح الجلسة (Cookie) من متصفح المستخدم
        // أولاً: نحصل على إعدادات الكوكي الحالية (المسار، الدومين، إلخ) عشان نرسل أمر الحذف بنفس الإعدادات
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
  
}
