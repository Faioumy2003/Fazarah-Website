<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.html');
    exit();
}

require_once 'config.php';

$email = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// الحصول على محتوى المستخدم
$stmt = $conn->prepare("SELECT * FROM content WHERE author_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$content = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// الحصول على تعليقات المستخدم
$stmt = $conn->prepare("SELECT c.*, ct.title FROM comments c JOIN content ct ON c.content_id = ct.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">منصة فزارة التعليمية</div>
        <nav>
            <ul>
                <li><a href="index.html">الرئيسية</a></li>
                <li><a href="cms.html">إدارة المحتوى</a></li>
                <li><a href="#" onclick="logout()">تسجيل خروج</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <div class="profile-section">
            <h1>الملف الشخصي</h1>
            <div class="user-info">
                <h2>معلومات المستخدم</h2>
                <p>البريد الإلكتروني: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>تاريخ التسجيل: <?php echo date('Y/m/d', strtotime($user['created_at'])); ?></p>
            </div>

            <div class="user-content">
                <h2>المحتوى المنشور</h2>
                <?php if (count($content) > 0): ?>
                    <ul class="content-list">
                    <?php foreach ($content as $item): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p>القسم: <?php echo htmlspecialchars($item['section']); ?></p>
                            <p>تاريخ النشر: <?php echo date('Y/m/d', strtotime($item['created_at'])); ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>لا يوجد محتوى منشور</p>
                <?php endif; ?>
            </div>

            <div class="user-comments">
                <h2>التعليقات</h2>
                <?php if (count($comments) > 0): ?>
                    <ul class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <li>
                            <p>على: <?php echo htmlspecialchars($comment['title']); ?></p>
                            <p>التعليق: <?php echo htmlspecialchars($comment['comment']); ?></p>
                            <p>تاريخ التعليق: <?php echo date('Y/m/d', strtotime($comment['created_at'])); ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>لا توجد تعليقات</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function logout() {
        fetch('logout.php')
            .then(() => window.location.href = 'login.html');
    }
    </script>
</body>
</html>