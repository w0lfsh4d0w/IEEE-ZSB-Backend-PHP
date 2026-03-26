<?php

namespace core;

class Container
{
    protected $bindings= [];
    public function bind($key, $resolver) 
    {
        $this->bindings[$key]=$resolver;
    }

    public function resolve($key) 
    {
        if (! array_key_exists($key, $this->bindings)) {
            // إذا لم يكن موجوداً، نعترض ونرمي خطأ برمجي (Exception)
            throw new \Exception("No matching binding found for {$key}");
        }

        // 2. إذا كان موجوداً، نجلب الدالة المسؤولة عن بناء الكائن
        $resolver = $this->bindings[$key];

        // 3. نقوم بتشغيل الدالة وإرجاع النتيجة
        return call_user_func($resolver);

    }
}
