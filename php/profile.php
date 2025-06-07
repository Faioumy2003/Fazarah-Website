<?php
session_start();
require_once 'php/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

$user = $_SESSION['user'];
$userid = $user['id'];

// استعلام الدروس المجتازة والاختبارات
$passed = $_SESSION['quiz_passed'] ?? [];

// استعلام الشهادات (مثلاً من جدول منفصل إذا كنت تخزنها في db)
// هنا نفترض أنها من الجلسة فقط

?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم المستخدم</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">منصة فزارة التعليمية</div>
        <!-- ... نفس قائمة التنقل ... -->
    </header>
    <div class="main-container">
        <h1>مرحبًا <?= htmlspecialchars($user['username']) ?></h1>
        <h3>دروس اجتزتها:</h3>
        <ul>
            <?php foreach ($passed as $lid => $status): ?>
            <?php if ($status): ?>
                <li>
                    الدرس رقم <?= (int)$lid ?> - <a href="certificate.php?lesson=<?= (int)$lid ?>" target="_blank">تحميل الشهادة</a>
                </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <!-- يمكنك إضافة بيانات المستخدم، الأسئلة التي طرحها، الدروس المفضلة، ...إلخ -->
    </div>
</body>
</html>