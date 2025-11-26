<?php
require_once 'config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(trim($_POST['username']));
    $employee_id = trim($_POST['employee_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $tel = trim($_POST['tel']);
    $role = $_POST['role'];
    $status = $_POST['status'];


    // Check if username already exists
    $sqlCheck = 'SELECT COUNT(*) FROM user WHERE username = :username';
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':username' => $username]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        echo '<script>';
        echo 'alert("Error: Username already exists. Please choose a different username.");';
        echo 'window.location.href = "member_create.php";';
        echo '</script>';
        exit();
    }

    // Check if employee_id already exists
    $sqlCheckEmp = 'SELECT COUNT(*) FROM member WHERE employee_id = :employee_id';
    $stmtCheckEmp = $pdo->prepare($sqlCheckEmp);
    $stmtCheckEmp->execute([':employee_id' => $employee_id]);
    $countEmp = $stmtCheckEmp->fetchColumn();

    if ($countEmp > 0) {
        echo '<script>';
        echo 'alert("Error: Employee ID already exists. Please check the Employee ID.");';
        echo 'window.location.href = "member_create.php";';
        echo '</script>';
        exit();
    }

    $pdo->beginTransaction();

    try {
        //Insert Member
        $sql = 'INSERT INTO member (employee_id, first_name, last_name, email, tel, status) VALUES (:employee_id, :first_name, :last_name, :email, :tel, :status)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':tel' => $tel,
            ':status' => $status
        ]);
        $member_id = $pdo->lastInsertId();

        //Insert User
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        $sqlUser = 'INSERT INTO user (username, password, member_id, role) VALUES (:username, :password, :member_id, :role)';
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':username' => $username,
            ':password' => $defaultPassword,
            ':member_id' => $member_id,
            ':role' => $role
        ]);

        $pdo->commit();

        echo '<script>';
        echo 'alert("Member created successfully! Default password is 123456.");';
        echo 'window.location.href = "member_1.php";';
        echo '</script>';
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die('Error: ' . $e->getMessage());
    }
} else {
    header('Location: member_create.php');
    exit();
}
