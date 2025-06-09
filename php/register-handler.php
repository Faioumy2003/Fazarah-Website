<?php
// نسخة من register-handler.php لنقلها إلى مجلد php/
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "islamic_website";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

function sendVerificationEmail($email, $token) {
    $subject = "تأكيد البريد الإلكتروني";
    $message = "يرجى الضغط على الرابط التالي لتأكيد بريدك الإلكتروني: \n";
    $message .= "http://yourwebsite.com/verify.php?email=$email&token=$token";
    $headers = "From: no-reply@yourwebsite.com";
    mail($email, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('طلب غير صالح.');
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$confirm = filter_var($_POST['confirm-password'], FILTER_SANITIZE_STRING);
$first = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
$last = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
$gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
$national_id = filter_var($_POST['national_id'], FILTER_SANITIZE_STRING);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('البريد الإلكتروني غير صالح.');
}
if ($password !== $confirm) {
    die('كلمتا المرور غير متطابقتين.');
}
if (strlen($password) < 6) {
    die('كلمة المرور يجب أن تكون 6 أحرف على الأقل.');
}
if (!$first || !$last || !$gender || !$national_id) {
    die('يرجى ملء جميع الحقول المطلوبة.');
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die('البريد الإلكتروني مستخدم بالفعل.');
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(16));

$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, gender, national_id, is_active, activation_code) VALUES (?, ?, ?, ?, ?, ?, 0, ?)");
$activation_code = bin2hex(random_bytes(16));
$stmt->bind_param("sssssss", $first, $last, $email, $hashed, $gender, $national_id, $activation_code);
if ($stmt->execute()) {
    sendVerificationEmail($email, $activation_code);
    echo 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني لتأكيد الحساب.';
} else {
    echo 'حدث خطأ أثناء التسجيل.';
}
$stmt->close();
$conn->close();
?>
