<?php
require_once 'config/connectdb.php';
?>
<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sample Library</title>
    <link href="vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free-7.1.0-web/css/all.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-2.3.5/datatables.min.css" rel="stylesheet" integrity="sha384-49/RW1o98YG2C2zlWgS77FLSrXw99u/R5gTv26HOR4VWXy7jVEt8iS/cfDn6UtHE" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="mt-5">Welcome to the Sample Library</h1>
                <h4 class="mt-3">Your gateway to knowledge and adventure!</h4>
                <h5 class="mt-3">Member Version 4</h5>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col">
                        <a href="index.php" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
                    </div>
                    <div class="col text-end">
                        <a href="member_create_ajax.php" class="btn btn-primary mb-3"><i class="fa-solid fa-plus"></i> Add Member</a>
                    </div>
                </div>

                <table class="table table-bordered" id="memberTable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Fullname</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = 'SELECT * FROM user, member WHERE member.member_id = user.member_id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $r = $stmt->fetchAll();

                        foreach ($r as $row) {
                        ?>
                            <tr>
                                <td><?= $row['username'] ?></td>
                                <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                <td><?= $row['role'] ?></td>
                                <td>
                                    <?php
                                    if ($row['status'] == 'active') {
                                        echo '<span class="badge bg-success">Active</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">Inactive</span>';
                                    }

                                    //echo ($row['status'] == 'active') ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="member_edit_ajax.php?id=<?= $row['member_id'] ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <button class="btn btn-danger btn-sm" onclick="deleteMember(<?= $row['member_id'] ?>);"><i class="fa-solid fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.3.5/datatables.min.js" integrity="sha384-0y3De3Rxhdkd4JPUzXfzK6J+7DyDlhLosIUV2OnIgn3Lh1i86pheXHOYUHK85Vwz" crossorigin="anonymous"></script>
</body>

</html>

<script>
    $(document).ready(function() {
        $('#memberTable').DataTable();
    });

    function deleteMember(memberId) {
        if (confirm('Are you sure you want to delete this member?')) {
            $.ajax({
                url: 'api/member_api.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    member_id: memberId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error deleting member. Please try again.');
                }
            });
        }
    }
</script>