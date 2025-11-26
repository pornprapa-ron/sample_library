<?php
include 'config/connectdb.php';

if (!isset($_GET['id'])) {
    echo '<script>';
    echo 'alert("Error: No member ID provided.");';
    echo 'window.location.href = "member_1.php";';
    echo '</script>';
    exit();
}

$member_id = $_GET['id'];

$sql = 'SELECT * FROM user, member WHERE member.member_id = user.member_id AND member.member_id = :member_id';
$stmt = $pdo->prepare($sql);
$stmt->execute([':member_id' => $member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    echo '<script>';
    echo 'alert("Error: Member not found.");';
    echo 'window.location.href = "member_1.php";';
    echo '</script>';
    exit();
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
                <h5 class="mt-3">Member Update Version 4</h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Member
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="member_id" value="<?= htmlspecialchars($member['member_id']) ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" disabled value="<?= $member['username'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" disabled value="<?= $member['employee_id'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $member['first_name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $member['last_name'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $member['email'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tel" class="form-label">Tel</label>
                        <input type="text" class="form-control" id="tel" name="tel" value="<?= $member['tel'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="member" <?= $member['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                            <option value="admin" <?= $member['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Member</button>
                </form>
            </div>
        </div>

    </div>
    <script src="vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
</body>

</html>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'api/member_api.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = 'member_4.php';
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        });
    });
</script>