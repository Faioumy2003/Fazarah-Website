<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (register_user($email, $password)) {
        header('Location: ../login.html?registered=1');
        exit;
    } else {
        echo "حدث خطأ أثناء التسجيل، ربما البريد الإلكتروني مستخدم بالفعل.";
    }
}
?>