<?php
session_start();
require_once 'php/db.php';
require('php/fpdf/fpdf.php');

$lesson_id = intval($_GET['lesson']);
$user = $_SESSION['user']['username'] ?? 'مستخدم';

if (empty($_SESSION['quiz_passed'][$lesson_id])) {
    die("عذراً، يجب اجتياز الاختبار للحصول على الشهادة.");
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',24);
$pdf->Cell(0,40,'شهادة اجتياز',0,1,'C');
$pdf->SetFont('Arial','',16);
$pdf->Cell(0,15,"تمنح إلى:",0,1,'C');
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,18, $user, 0, 1, 'C');
$pdf->SetFont('Arial','',14);
$pdf->Cell(0,10,"لاستكماله بنجاح اختبار الدرس رقم $lesson_id",0,1,'C');
$pdf->Output('I', 'certificate.pdf');
exit;
?>