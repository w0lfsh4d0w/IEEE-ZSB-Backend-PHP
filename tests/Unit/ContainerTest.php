<?php
use core\Container ;
// نبدأ الاختبار ونعطيه وصفاً واضحاً
test('it can resolve something out of the container', function () {
    
    // 1. Arrange: ترتيب البيئة
    $container = new Container(); 
    
    $container->bind('foo', function () {
        return 'bar';
    });

    // 2. Act: التنفيذ
    $result = $container->resolve('foo');

    // 3. Expect (Assert): التحقق
    expect($result)->toEqual('bar');

});