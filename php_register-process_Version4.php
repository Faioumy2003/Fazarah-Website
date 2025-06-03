<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $gender = $_POST['gender'];
    $national_id = trim($_POST['national_id']);
    $is_active = 0;
    $code = rand(100000,999999);
    $hash = password_hash($pass, PASSWORD_BCRYPT);

    // تحقق أن البريد أو الرقم القومي غير مستخدم
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=? OR national_id=?");
    $stmt->execute([$email, $national_id]);
    if($stmt->fetch()) {
        die("البريد الإلكتروني أو الرقم القومي مستخدم من قبل.");
    }

    // سجل المستخدم كغير مفعل
    $stmt = $pdo->prepare("INSERT INTO users (first_name,last_name,email,password,gender,national_id,is_active,activation_code) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$first,$last,$email,$hash,$gender,$national_id,$is_active,$code]);

    // أرسل كود التفعيل
    mail($email, "رمز التفعيل - منصة فزارة", "كود التفعيل الخاص بك: $code");

    // اطلب من المستخدم إدخال كود التفعيل
    header("Location: activate.html?email=" . urlencode($email));
    exit;
}
?>