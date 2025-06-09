<?php
// اختبار آلي لتسجيل مستخدم جديد في الموقع
// يجب تشغيل هذا السكريبت من سطر الأوامر أو متصفح محلي

$url = 'http://localhost/register-handler.php';

$data = [
    'email' => 'testuser' . rand(1000,9999) . '@example.com',
    'password' => 'Test1234',
    'confirm-password' => 'Test1234'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "فشل الاتصال بصفحة التسجيل.\n";
} else {
    echo "نتيجة الاختبار:\n";
    echo $result;
}
?>
