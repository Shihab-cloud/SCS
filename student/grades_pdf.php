<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /smart_cloud_system/login_step1.php");
    exit;
}

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

$student_id = $_SESSION['login_user'] ?? '';

// Fetch student info
$stuStmt = $conn->prepare("SELECT first_name, last_name FROM Students WHERE student_id = ?");
$stuStmt->bind_param("s", $student_id);
$stuStmt->execute();
$stu = $stuStmt->get_result()->fetch_assoc();
$fullName = trim(($stu['first_name'] ?? '') . ' ' . ($stu['last_name'] ?? ''));

// Fetch grades
$gStmt = $conn->prepare(" SELECT c.course_id, c.course_name, r.marks_obtained, r.grade
                          FROM Results r
                          JOIN Courses c ON c.course_id = r.course_id
                          WHERE r.student_id = ?
                          ORDER BY c.course_id");

$gStmt->bind_param("s", $student_id);
$gStmt->execute();
$grades = $gStmt->get_result();

// ---- Build PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Grade Sheet', 0, 1, 'C');
$pdf->Ln(2);

// Student info
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Student ID: ' . $student_id, 0, 1);
$pdf->Cell(0, 8, 'Name: ' . $fullName, 0, 1);
$pdf->Ln(3);

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(35, 10, 'Course ID', 1, 0, 'C');
$pdf->Cell(95, 10, 'Course Name', 1, 0, 'C');
$pdf->Cell(25, 10, 'Marks', 1, 0, 'C');
$pdf->Cell(25, 10, 'Grade', 1, 1, 'C');

// Rows
$pdf->SetFont('Arial', '', 12);
while ($row = $grades->fetch_assoc()) {
    $pdf->Cell(35, 9, $row['course_id'], 1);
    $pdf->Cell(95, 9, $row['course_name'], 1);
    $pdf->Cell(25, 9, $row['marks_obtained'], 1, 0, 'C');
    $pdf->Cell(25, 9, $row['grade'], 1, 1, 'C');
}

// Make sures that there is absolutely no previous output in the buffer:
if (ob_get_length()) {
    ob_end_clean();
}

// Send the PDF
$pdf->Output('D', "grade_sheet_{$student_id}.pdf");
exit;