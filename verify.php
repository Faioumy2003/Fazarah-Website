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

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND token = ? AND verified = 0");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        $stmt = $conn->prepare("UPDATE users SET verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "تم تأكيد حسابك بنجاح. يمكنك الآن تسجيل الدخول.";
        } else {
            echo "حدث خطأ أثناء تأكيد الحساب.";
        }
        $stmt->close();
    } else {
        echo "رابط التحقق غير صالح أو الحساب تم تأكيده مسبقاً.";
    }
}
$conn->close();
?>
