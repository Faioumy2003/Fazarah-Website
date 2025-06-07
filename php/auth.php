<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // تسجيل مستخدم جديد
    if ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username && $email && $password) {
            // تحقق إذا كان البريد مستخدم بالفعل
            $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                header("Location: ../register.html?error=exists");
                exit();
            }

            // تشفير كلمة المرور
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $username, $email, $hashed);
            $stmt->execute();
            header("Location: ../login.html?success=registered");
            exit();
        } else {
            header("Location: ../register.html?error=invalid");
            exit();
        }
    }

    // تسجيل الدخول
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email && $password) {
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email=? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user'] = [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'email' => $email
                    ];
                    header("Location: ../index.html");
                    exit();
                }
            }
        }
        header("Location: ../login.html?error=invalid");
        exit();
    }
}

header("Location: ../login.html");
exit();
?>
