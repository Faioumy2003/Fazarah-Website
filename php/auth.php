<?php
require_once 'db.php';

// تسجيل مستخدم جديد
function register_user($email, $password) {
    global $pdo;
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    return $stmt->execute([$email, $hash]);
}

// تسجيل دخول
function login_user($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        return true;
    }
    return false;
}

// استعادة كلمة المرور: فقط مثال (أرسل رابط لبريد المستخدم)
function send_reset_link($email) {
    // هنا ترسل بريد إلكتروني للمستخدم مع رابط لإعادة تعيين كلمة المرور
    // يمكن استخدام PHPMailer أو mail() من PHP
    // هذه الوظيفة تحتاج إعداد حقيقي لSMTP
    return true;
}
?>