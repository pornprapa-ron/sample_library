<?php
require_once 'config/connectdb.php';

if (!isset($_GET['id'])) {
    echo '<script>';
    echo 'alert("Error: No member ID provided.");';
    echo 'window.location.href = "member_3.php";';
    echo '</script>';
    exit();
}

$member_id = $_GET['id'];
?>

<script>
    window.location.href = "member_process.php?action=delete&member_id=<?= htmlspecialchars($member_id) ?>";
</script>