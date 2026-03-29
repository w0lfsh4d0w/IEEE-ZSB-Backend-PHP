<?php
use core\Response;
function dd($value)
  {
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
  }
  function isUrl($value)
  {
    return $_SERVER['REQUEST_URI']===$value ; 
  }// Updated at Sun Mar 15 11:47:50 PM EET 2026
// Updated at Sun Mar 15 11:51:36 PM EET 2026
function abort($code=404) {
    http_response_code($code);
        require base_path("views/response{$code}.php");
        die();
}
function Authorize($condition,$status=Response::FORBIDDEN)  {
  if(!$condition)
    {
      abort($status);
    }
}
function base_path($path)
{
  return BASE_BATH . $path;
}
function view($path,$attributes=[])  {
  extract($attributes);
  require base_path('views/'.$path);
}
function login ($user)
{
   $_SESSION['user'] = [
        'email' => $user['email']
    ];
    
    session_regenerate_id(true);
}
function logout()
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
setcookie('PHPSESSID','',time()-3600,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
}