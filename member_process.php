<?php
require_once 'config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action']) && $_GET['action'] === 'delete') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if ($action === 'create') {
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
            echo 'window.location.href = "member_create_api.php";';
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
            echo 'window.location.href = "member_create_api.php";';
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
            echo 'window.location.href = "member_3.php";';
            echo '</script>';
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die('Error: ' . $e->getMessage());
        }
    }

    if ($action === 'update') {
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
            echo 'window.location.href = "member_3.php";';
            echo '</script>';
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo '<script>';
            echo 'alert("Error: Could not update member. Please try again.");';
            echo 'window.location.href = "member_edit_api.php?id=' . htmlspecialchars($member_id) . '";';
            echo '</script>';
            exit();
        }
    }

    if ($action === 'delete') {

        $member_id = $_GET['member_id'];

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
            echo 'window.location.href = "member_3.php";';
            echo '</script>';
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo '<script>';
            echo 'alert("Error: Could not delete member. Please try again.");';
            echo 'window.location.href = "member_3.php";';
            echo '</script>';
            exit();
        }
    }
} else {
    header('Location: member_3.php');
    exit();
}
