<?php
session_start();
require_once 'db.php';

$lesson_id = intval($_POST['lesson_id']);
$selected = intval($_POST['q1'] ?? 0);

$stmt = $conn->prepare("SELECT correct_option FROM quizzes WHERE lesson_id=? LIMIT 1");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$stmt->bind_result($correct);
$stmt->fetch();
$stmt->close();

if ($selected === $correct) {
    $_SESSION['quiz_passed'][$lesson_id] = true;
    header("Location: ../certificate.php?lesson=$lesson_id");
    exit;
} else {
    echo "إجابة غير صحيحة. حاول مرة أخرى!";
}