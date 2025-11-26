<?php
require_once 'config/connectdb.php';

if (isset($_POST['submit'])) {
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
        echo 'window.location.href = "member_create_callback.php";';
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
        echo 'window.location.href = "member_create_callback.php";';
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
        echo 'window.location.href = "member_2.php";';
        echo '</script>';
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die('Error: ' . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sample Library</title>
    <link href="vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free-7.1.0-web/css/all.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="mt-5">Welcome to the Sample Library</h1>
                <h4 class="mt-3">Your gateway to knowledge and adventure!</h4>
                <h5 class="mt-3">Member Create Version 2</h5>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                Create New Member
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="tel" class="form-label">Tel</label>
                        <input type="text" class="form-control" id="tel" name="tel" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Create Member</button>
                </form>
            </div>
        </div>

    </div>
    <script src="vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
</body>

</html>