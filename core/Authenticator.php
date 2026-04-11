<?php

namespace core;

use core\App;
use core\Database;

class Authenticator
{
    public function attempt($email, $password)
    {
        // mtach the credintals 
        $user = App::resolve(Database::class)->query('select * from users where email = :email', [
            'email' => $email
        ])->find();

        // // log in it the credintals match 
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $this->login([
                    'email' => $email
                ]);
                return true;
            }
        }
        return false;
    }
    public function login($user)
    {
        $_SESSION['user'] = [
            'email' => $user['email']
        ];

        session_regenerate_id(true);
    }
    public function logout()
    {
        // 1. تفريغ المصفوفة من الذاكرة الحالية للسكربت
        // هذا يؤثر فقط على المستخدم الحالي (صاحب الطلب) ولا يمس المستخدمين الآخرين
        $_SESSION = [];

        // 2. تدمير الملف الفعلي من الهارد ديسك الخاص بالسيرفر
        // لحماية النظام من أي هاكر قد يكون سرق مُعرف الجلسة (Session ID)
        session_destroy();

        // 3. حذف مفتاح الجلسة (Cookie) من متصفح المستخدم
        // أولاً: نحصل على إعدادات الكوكي الحالية (المسار، الدومين، إلخ) عشان نرسل أمر الحذف بنفس الإعدادات
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}
