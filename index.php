<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة فزارة التعليمية</title>
    <!-- باقي كود الواجهة الأمامية كما هو -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- ... -->
</head>
<body>
    <!-- Navbar وكل شيء كما هو في الواجهة الأمامية -->
</body>
</html>