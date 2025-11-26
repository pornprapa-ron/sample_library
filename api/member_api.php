<?php
header('Content-Type: application/json');
require_once '../config/connectdb.php';

$action = $_REQUEST['action'] ?? '';

// Action: Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
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
        echo json_encode(['status' => 'error', 'message' => 'Username already exists. Please choose a different username.']);
        exit();
    }

    // Check if employee_id already exists
    $sqlCheckEmp = 'SELECT COUNT(*) FROM member WHERE employee_id = :employee_id';
    $stmtCheckEmp = $pdo->prepare($sqlCheckEmp);
    $stmtCheckEmp->execute([':employee_id' => $employee_id]);
    $countEmp = $stmtCheckEmp->fetchColumn();

    if ($countEmp > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID already exists. Please check the Employee ID.']);
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

        echo json_encode(['status' => 'success', 'message' => 'Member created successfully.']);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: Could not create member. Please try again.']);
        exit();
    }
}

// Action: Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {

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

        echo json_encode(['status' => 'success', 'message' => 'Member updated successfully.']);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: Could not update member. Please try again.']);
        exit();
    }
}

// Action: Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {

    $member_id = $_POST['member_id'];

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
        echo json_encode(['status' => 'success', 'message' => 'Member deleted successfully.']);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: Could not delete member. Please try again.']);
        exit();
    }
}
