<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.html');
    exit();
}

require_once 'config.php';

// إحصائيات المحتوى
$contentStats = $conn->query("
    SELECT section, COUNT(*) as count 
    FROM content 
    GROUP BY section
")->fetch_all(MYSQLI_ASSOC);

// إحصائيات المستخدمين
$userStats = $conn->query("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN verified = 1 THEN 1 ELSE 0 END) as verified_users
    FROM users
")->fetch_assoc();

// إحصائيات التعليقات
$commentStats = $conn->query("
    SELECT 
        content.section,
        COUNT(comments.id) as comment_count
    FROM content
    LEFT JOIN comments ON content.id = comments.content_id
    GROUP BY content.section
")->fetch_all(MYSQLI_ASSOC);

// المحتوى الأكثر تعليقاً
$popularContent = $conn->query("
    SELECT 
        content.title,
        content.section,
        COUNT(comments.id) as comment_count
    FROM content
    LEFT JOIN comments ON content.id = comments.content_id
    GROUP BY content.id
    ORDER BY comment_count DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إحصائيات الموقع</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="navbar">
        <div class="logo">منصة فزارة التعليمية</div>
        <nav>
            <ul>
                <li><a href="index.html">الرئيسية</a></li>
                <li><a href="cms.html">إدارة المحتوى</a></li>
                <li><a href="profile.php">الملف الشخصي</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <h1>إحصائيات الموقع</h1>
        
        <div class="stats-grid">
            <div class="stats-card">
                <h2>إحصائيات المستخدمين</h2>
                <p>إجمالي المستخدمين: <?php echo $userStats['total_users']; ?></p>
                <p>المستخدمون المفعلون: <?php echo $userStats['verified_users']; ?></p>
                <canvas id="userChart"></canvas>
            </div>

            <div class="stats-card">
                <h2>إحصائيات المحتوى</h2>
                <canvas id="contentChart"></canvas>
            </div>

            <div class="stats-card">
                <h2>إحصائيات التعليقات</h2>
                <canvas id="commentChart"></canvas>
            </div>

            <div class="stats-card">
                <h2>المحتوى الأكثر تفاعلاً</h2>
                <ul>
                <?php foreach ($popularContent as $content): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($content['title']); ?></strong>
                        <span>(<?php echo $content['comment_count']; ?> تعليق)</span>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
    // رسم المخططات البيانية
    const contentData = <?php echo json_encode($contentStats); ?>;
    const commentData = <?php echo json_encode($commentStats); ?>;
    const userData = {
        total: <?php echo $userStats['total_users']; ?>,
        verified: <?php echo $userStats['verified_users']; ?>
    };

    // مخطط المستخدمين
    new Chart(document.getElementById('userChart'), {
        type: 'pie',
        data: {
            labels: ['مفعل', 'غير مفعل'],
            datasets: [{
                data: [userData.verified, userData.total - userData.verified],
                backgroundColor: ['#4CAF50', '#FF5252']
            }]
        }
    });

    // مخطط المحتوى
    new Chart(document.getElementById('contentChart'), {
        type: 'bar',
        data: {
            labels: contentData.map(item => item.section),
            datasets: [{
                label: 'عدد المقالات',
                data: contentData.map(item => item.count),
                backgroundColor: '#2196F3'
            }]
        }
    });

    // مخطط التعليقات
    new Chart(document.getElementById('commentChart'), {
        type: 'line',
        data: {
            labels: commentData.map(item => item.section),
            datasets: [{
                label: 'عدد التعليقات',
                data: commentData.map(item => item.comment_count),
                borderColor: '#9C27B0',
                fill: false
            }]
        }
    });
    </script>
</body>
</html>
