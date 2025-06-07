<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (send_reset_link($email)) {
        header('Location: ../login.html?reset=1');
        exit;
    } else {
        echo "تعذر إرسال رابط استعادة كلمة المرور. تحقق من البريد الإلكتروني.";
    }
}
?>