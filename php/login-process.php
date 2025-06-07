<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $result = login_user($email, $password);

    if ($result === true) {
        header('Location: ../index.php');
        exit;
    } else {
        echo "بيانات الدخول غير صحيحة!";
    }
}
?>