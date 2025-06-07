<?php
// ملف الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "islamic_website";
$user = "root";
$pass = "";

// الاتصال باستخدام PDO مع معالجة الأخطاء
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>