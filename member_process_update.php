<?php
require_once 'config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $tel = trim($_POST['tel']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $pdo->beginTransaction();

    try {
        // Update Member
        $sql = 'UPDATE member SET 
                    first_name = :first_name, last_name = :last_name, email = :email, tel = :tel, status = :status 
                WHERE 
                    member_id = :member_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':tel' => $tel,
            ':status' => $status,
            ':member_id' => $member_id
        ]);

        // Update User Role
        $sqlUser = 'UPDATE user SET role = :role WHERE member_id = :member_id';
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':role' => $role,
            ':member_id' => $member_id
        ]);

        $pdo->commit();

        echo '<script>';
        echo 'alert("Member updated successfully.");';
        echo 'window.location.href = "member_1.php";';
        echo '</script>';
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo '<script>';
        echo 'alert("Error: Could not update member. Please try again.");';
        echo 'window.location.href = "member_edit.php?id=' . htmlspecialchars($member_id) . '";';
        echo '</script>';
        exit();
    }
} else {
    header('Location: member_create.php');
    exit();
}
