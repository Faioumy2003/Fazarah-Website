<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.html');
    exit;
}

require_once 'db.php';

// ... استقبال باقي بيانات الدرس السابقة

// استقبال ورفع الملفات
$video_filename = "";
$pdf_filename = "";
$image_filename = "";

if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $video_filename = uniqid()."_".basename($_FILES['video']['name']);
    move_uploaded_file($_FILES['video']['tmp_name'], "../uploads/$video_filename");
}
if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == 0) {
    $pdf_filename = uniqid()."_".basename($_FILES['pdf']['name']);
    move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/$pdf_filename");
}
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_filename = uniqid()."_".basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image_filename");
}

// ... ثم أضف أسماء الملفات إلى قاعدة البيانات مع بيانات الدرس
