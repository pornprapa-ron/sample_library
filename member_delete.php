<?php
require_once 'config/connectdb.php';

if (!isset($_GET['id'])) {
    echo '<script>';
    echo 'alert("Error: No member ID provided.");';
    echo 'window.location.href = "member_1.php";';
    echo '</script>';
    exit();
}

$member_id = $_GET['id'];

$pdo->beginTransaction();
try {
    // Delete User
    $sqlUser = 'DELETE FROM user WHERE member_id = :member_id';
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([':member_id' => $member_id]);
    
    // Delete Member
    $sqlMember = 'DELETE FROM member WHERE member_id = :member_id';
    $stmtMember = $pdo->prepare($sqlMember);
    $stmtMember->execute([':member_id' => $member_id]);

    $pdo->commit();
    echo '<script>';
    echo 'alert("Member deleted successfully.");';
    echo 'window.location.href = "member_1.php";';
    echo '</script>';
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo '<script>';
    echo 'alert("Error: Could not delete member. Please try again.");';
    echo 'window.location.href = "member_1.php";';
    echo '</script>';
    exit();
}
