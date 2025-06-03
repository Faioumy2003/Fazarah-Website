<?php
// بدء جلسة المستخدم
session_start();

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "islamic_website";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من إرسال البيانات عبر POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // التحقق من صحة البيانات
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('البريد الإلكتروني غير صالح.');
    }

    // البحث عن المستخدم في قاعدة البيانات
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $email;
            header('Location: welcome.html');
            exit();
        } else {
            echo 'كلمة المرور غير صحيحة.';
        }
    } else {
        echo 'البريد الإلكتروني غير موجود.';
    }

    $stmt->close();
} else {
    echo 'طريقة الطلب غير صحيحة.';
}

// إضافة تسجيل الدخول باستخدام Google OAuth
require_once 'vendor/autoload.php';

use Google\Client;

$client = new Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://yourwebsite.com/login-handler.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name = $google_account_info->name;

        // تحقق من وجود المستخدم في قاعدة البيانات
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['user'] = $email;
            header('Location: welcome.html');
        } else {
            // إضافة المستخدم الجديد
            $stmt = $conn->prepare("INSERT INTO users (email, name, verified) VALUES (?, ?, 1)");
            $stmt->bind_param("ss", $email, $name);
            $stmt->execute();
            $_SESSION['user'] = $email;
            header('Location: welcome.html');
        }
        $stmt->close();
    } else {
        echo 'خطأ أثناء تسجيل الدخول باستخدام Google.';
    }
} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

$conn->close();
?>